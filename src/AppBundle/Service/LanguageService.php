<?php
/**
 * Created by PhpStorm.
 * User: laurentiu
 * Date: 9/3/16
 * Time: 11:12 PM
 */

namespace AppBundle\Service;


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
        $words = explode(" ",$text);
        $pdo = $this->createPDO();
        $sql = '';
        $firstNode = [];
        $lastNode = [];
        $pendingAction = '';
        $at = '';
        $on = '';
        $date = '';
        foreach ($words as $word) {
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
                $date = date('Y-m-d H:i:s',strtotime("next" . $word));
                $this->graphService->createNode('Date', [ 'name' => $date ]);
                $lastNode['labelKey'] = 'name';
                $lastNode['labelValue'] = $lastNode['labels']['name'];
                $lastNode['type'] = 'Activity';
                $this->graphService->createRelationship($lastNode, ['labelKey' => 'name', 'labelValue' => $date, 'type'=>'Date'], $on);
            }

            if ($word == 'on') {
                $on = $word;
            }

            if ($word == 'at') {
                $at = $word;
            }

            if (!empty($at) && isset($date)) {
                $this->graphService->createNode('Location', [ 'name' => $word ]);
                $this->graphService->createRelationship(['labelKey' => 'name', 'labelValue' => $date, 'type'=>'Date'], ['labelKey' => 'name', 'labelValue' => $word, 'type' => 'Location'], $at);
            }

            if (count($tags) == 1 && $tags['0']['0'] == 'NP') {
                $firstNode['type'] = 'Person';
                $firstNode['labels'] =
                    [
                        'name' => $word
                    ];
                $this->graphService->createNode($firstNode['type'], $firstNode['labels']);

                continue;
            }

            foreach ($tags as $tag) {
                if (in_array($tag['0'], $this->verbsTag)) {
                    if (!empty($firstNode) && !empty($lastNode)) {
                        $this->graphService->createRelationship($firstNode, $lastNode, $word);
                        $d = json_encode($pendingAction . 'asdas');
                        var_dump($d);
                    } else {
                        $pendingAction = $word;
                    }
                    continue 2;

                } elseif (in_array($tag['0'], $this->nounsTag)) {
                        $lastNode['type'] = 'Activity';
                        $lastNode['labels'] =
                            [
                                'name' => $word
                            ];
                        $this->graphService->createNode($lastNode['type'], $lastNode['labels']);
                        if (!empty ($pendingAction)) {
                            $firstNode['labelKey'] = 'name';
                            $firstNode['labelValue'] = $firstNode['labels']['name'];
                            $lastNode['labelKey'] = 'name';
                            $lastNode['labelValue'] = $lastNode['labels']['name'];
                            $this->graphService->createRelationship($firstNode, $lastNode, $pendingAction);
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
        $dbhost = 'eis-sql8-dev';
        $dbusername = 'laurentiu.codrea';
        $dbpassword = 'parolalaurentiu';
        $dbname = 'zzz';

        $pdo = new \PDO("mysql:host=$dbhost;dbname=$dbname","$dbusername","$dbpassword");
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