<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return new Response('whoop');
    }

    /**
     * @Route("/plan", name="plan")
     *
     * function that handles the request from the user and passes it to the classification method
     */
    public function planAction(Request $request)
    {
        if (($request->request->get('info') !== null) && (!empty($request->get('info')))) {
            $this->dataClassification($request->request->get('info'));
        }

        return $this->render('default/plan.html.twig');
    }

    /**
     * @Route("/bulk-data", name="bulk-data")
     *
     * add bulk data from file to use in NLP training as well as recommendation
     */
    public function bulkDataAction()
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
     * Basic classification algorithm to handle texts and classify its data
     *
     * @param $text
     */
    public function dataClassification($text)
    {
        $words = explode(" ",$text);
        $pdo = $this->createPDO();
        $stmt = '';
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
                
            }
        }
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
}
