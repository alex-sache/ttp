<?php
/**
 * Created by PhpStorm.
 * User: laurentiu
 * Date: 9/3/16
 * Time: 11:12 PM
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LanguageService
{

    protected $nounsTag = array('NN','NNS','NP','NPS');
    protected $verbsTag = array('VB', 'VBD', 'VBG', 'VBN', 'VBP', 'VBZ');
    protected $daysOfTheWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

    /**
     * @var GraphService
     */
    protected $graphService;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @return EntityManagerInterface
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }

    /**
     * @return GraphService
     */
    public function getGraphService()
    {
        return $this->graphService;
    }

    /**
     * @param GraphService $graphService
     */
    public function setGraphService($graphService)
    {
        $this->graphService = $graphService;
    }

    /**
     * Basic classification algorithm to handle texts and classify its data
     *
     * @param $text
     */
    public function dataClassification($text)
    {
        $text = trim($text);
        if(!strlen($text)) return false;
        error_log($text);
        $words = explode(" ",$text);
        $pdo = $this->createPDO();
        $nodes =array();
        $relations= array();
        $firstNode = [];
        $lastNode = [];
        $pendingAction = '';
        $on = '';
        $nodeId = uniqid('ev');
        $nodes['eveniment'] = ['type' => 'EVENT',
            'labels' => ['NAME' => $text, 'UNI_EVENT'=> $nodeId]];

        foreach ($words as $indexWord => $word) {
            $sql = "SELECT d.tag
                      FROM lexic l
                        JOIN dictionary_to_lexic dtl ON l.id = dtl.word_id
                        JOIN dictionary d on dtl.tag_id = d.id
                      WHERE l.word = :tag
                      ORDER BY d.tag DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tag', $word);
            $stmt->execute();
            $tags = $stmt->fetchAll(\PDO::FETCH_NUM);

            if (!empty($on) && in_array($word, $this->daysOfTheWeek)) {
                //temporary non standard date format
                $date = date('d.m.Y',strtotime("next" . $word));
                $nodes['moment'] = ['type' => 'EVENT_TIME', 'labels' => ['NAME' => 'Day', 'DATE' => $date]];

                $relations['happens'] = [
                    'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => $nodes['eveniment']['UNI_EVENT']],
                    'nodeDestination' => ['type' => 'EVENT_TIME', 'labelKey' => 'DATE', 'labelValue' => $date],
                    'relType' => 'EVENT_HAPPENS'
                ];

                continue;
            }

            if ($word == 'at') {
                $nodes['location'] = ['type' => 'LOCATION', 'labels' => ['NAME' => $word]];
                $relations['at'] = [
                    'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => $nodes['eveniment']['UNI_EVENT']],
                    'nodeDestination' => ['type' => 'LOCATION', 'labelKey' => 'NAME', 'labelValue' => $word],
                    'relType' => 'EVENT_LOCATION'
                ];

                continue;
            }

            if (count($tags) == 1 && $tags['0']['0'] == 'NP') {
                $nodes[$indexWord] = ['type' => 'Person', 'labels' => ['NAME' => $word]];
                $relations['part'] = [
                    'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => $nodes['eveniment']['UNI_EVENT']],
                    'nodeDestination' => ['type' => 'LOCATION', 'labelKey' => 'NAME', 'labelValue' => $word],
                    'relType' => 'EVENT_LOCATION'
                ];
                $firstNode = $nodes[$indexWord];
                continue;
            }

            foreach ($tags as $tag) {
                if (in_array($tag['0'], $this->verbsTag)) {
                    if (!empty($firstNode) && !empty($lastNode)) {
                        $relations[$indexWord] = [
                            'nodeSource' => ['type' => $firstNode['type'], 'labelKey' => 'NAME', 'labelValue' => $firstNode['labels']['NAME']],
                            'nodeDestination' => ['type' => $lastNode['type'], 'labelKey' => 'NAME', 'labelValue' => $lastNode['labels']['NAME']],
                            'relType' => $word
                        ];
                    } else {
                        $pendingAction = $word;
                    }
                    continue 2;

                } elseif (in_array($tag['0'], $this->nounsTag)) {
                    $nodes[$indexWord] = ['type' => 'Activity', 'labels' => ['NAME' => $word]];
                    $lastNode= $nodes[$indexWord];
                    if (!empty ($pendingAction) && !empty($firstNode)) {
                        $relations[$indexWord] = [
                            'nodeSource' => ['type' => $firstNode['type'], 'labelKey' => 'NAME', 'labelValue' => $firstNode['labels']['NAME']],
                            'nodeDestination' => ['type' => $lastNode['type'], 'labelKey' => 'NAME', 'labelValue' => $lastNode['labels']['NAME']],
                            'relType' => $pendingAction
                        ];

                        $firstNode = $lastNode;
                        $lastNode = [];
                        $pendingAction = '';
                    }
                        continue 2;
//                    }
                }  else {
                    //TO DO: Train lexic and add options to add new tags
                }
            }

        }
        $this->getGraphService()->createGraph($nodes,$relations);
        return true;
    }

    /**
     * add bulk data from file to use in NLP training as well as recommendation
     */
    public function addBulkData()
    {
        $pdo = $this->createPDO();
        $file = fopen("../lexicon.txt", "r");

        if (!empty($file)) {
            while (!feof($file)) {
                $line = fgets($file);
                $words = explode(" ", $line);
                $words[count($words) - 1] = str_replace(PHP_EOL, "", $words[count($words) - 1]);

                $stmt1 = $pdo->prepare("INSERT INTO lexic(word) VALUES (:word) ON DUPLICATE KEY UPDATE word = :word");
                $stmt1->bindParam(":word", $words['0']);
                $stmt1->execute();

                for ($i = 1; $i < count($words); $i++) {

                    if (empty($tagId)) {
                        $sql1 = "INSERT INTO dictionary(tag) VALUES (:tag) ON DUPLICATE KEY UPDATE tag = :tag";
                        $stmt = $pdo->prepare($sql1);
                        $stmt->bindParam(':tag', $words[$i]);
                        $stmt->execute();
                    }

                    $sql2 = "SELECT id FROM dictionary WHERE tag = :tag";
                    $stmt = $pdo->prepare($sql2);
                    $stmt->bindParam(':tag', $words[$i]);
                    $stmt->execute();
                    $tagId = $stmt->fetchAll();

                    $sql3 = "SELECT id FROM lexic WHERE word = :word";
                    $stmt = $pdo->prepare($sql3);
                    $stmt->bindParam(':word', $words['0']);
                    $stmt->execute();
                    $wordId = $stmt->fetchAll();

                    if (!empty($tagId) && !empty($wordId) && !is_null($tagId['0']['id']) && !is_null($wordId['0']['id'])) {
                        $sql4 = "INSERT INTO dictionary_to_lexic(tag_id, word_id) VALUES (:tag, :word)";
                        $stmt = $pdo->prepare($sql4);
                        $stmt->bindParam(':tag', $tagId['0']['id']);
                        $stmt->bindParam(':word', $wordId['0']['id']);
                        $stmt->execute();
                    }
                }
            }
            fclose($file);
        }

        return true;
    }

    /**
     * helper function to create PDO
     *
     * @return \PDO
     */
    public function createPDO()
    {
        $pdo=$this->getEm()->getConnection();

        return $pdo;
    }


    public function getAutocompleteData()
    {
        $pdo = $this->createPDO();

        $stmt1 = $pdo->prepare("SELECT DISTINCT(l.word) from lexic l");
        $stmt1->execute();
        $result = $stmt1->fetchAll(\PDO::FETCH_OBJ);

        return json_encode($result);
    }
}