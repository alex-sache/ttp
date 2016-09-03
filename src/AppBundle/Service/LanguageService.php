<?php
/**
 * Created by PhpStorm.
 * User: laurentiu
 * Date: 9/3/16
 * Time: 11:12 PM
 */

namespace AppBundle\Service;


class LanguageService
{

    protected $nounsTag = array('NN','NNS','NP','NPS');
    protected $verbsTag = array('VB', 'VBD', 'VBG', 'VBN', 'VBP', 'VBZ');

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
        foreach ($words as $word) {
            $sql = "SELECT d.tag
                      FROM lexic l
                        JOIN dictionary_to_lexic dtl ON l.id = dtl.word_id
                        JOIN dictionary d on dtl.tag_id = d.id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tag', $words);
            $stmt->execute();
            $tags = $stmt->fetchAll();

            foreach ($tags as $tag) {
                if (in_array($tag, $this->nounsTag)) {
                    if (empty($firstNode)) {
                        $firstNode['type'] = 'Person';
                        $firstNode['labels'] =
                            [
                                'name' => 'I'
                            ];
                        $this->graphService->createNode($firstNode['type'], $firstNode['labels']);
                    } {
                        $lastNode['type'] = 'Action';
                        $lastNode['labels'] =
                            [
                                'name' => $word
                            ];
                        $this->graphService->createNode($lastNode['type'], $lastNode['labels']);
                        if (!empty ($pendingAction)) {
                            $this->graphService->createRelationship($firstNode, $lastNode, $pendingAction);
                            $firstNode = $lastNode;
                            $lastNode = [];
                        }
                    }
                } elseif (in_array($tag, $this->verbsTag)) {
                    if (!empty($firstNode) && !empty($lastNode)) {
                        $this->graphService->createRelationship($firstNode, $lastNode, $word);
                    } else {
                        $pendingAction = $word;
                    }

                } else {
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

        return new RedirectResponse($this->generateUrl('plan'));
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