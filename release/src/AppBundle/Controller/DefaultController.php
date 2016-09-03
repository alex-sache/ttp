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
     */
    public function planAction(Request $request)
    {
        if (($request->request->get('info') !== null) && (!empty($request->get('info')))) {
            var_dump($request->request->get('info'));
        }

        return $this->render('default/plan.html.twig');
    }

    /**
     * @Route("/bulk-data", name="bulk-data")
     */
    public function classificationAction()
    {
        $file = fopen("../lexicon.txt", "r");
        $dbhost = 'eis-sql8-dev';
        $dbusername = 'laurentiu.codrea';
        $dbpassword = 'parolalaurentiu';
        $dbname = 'zzz';

        $pdo = new \PDO("mysql:host=$dbhost;dbname=$dbname","$dbusername","$dbpassword");

        while(!feof($file)){
            $line = fgets($file);
            $words = explode(" ", $line);
            $words[count($words) - 1] = str_replace(PHP_EOL,"",$words[count($words) - 1]);

            $stmt1 = $pdo->prepare("INSERT INTO lexic(word) VALUES (:word) ON DUPLICATE KEY UPDATE word = :word");
            $stmt1->bindParam(":word", $words['0']);
            $stmt1->execute();

            for ($i = 1; $i < count($words); $i++) {

                if (empty($tagId)) {
                    $sql= "INSERT INTO dictionary(tag) VALUES (:tag) ON DUPLICATE KEY UPDATE tag = :tag";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':tag', $words[$i]);
                    $stmt->execute();
                }

                $sql= "SELECT id FROM dictionary WHERE tag = :tag";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':tag', $words[$i]);
                $stmt->execute();
                $tagId = $stmt->fetchAll();

                $sql= "SELECT id FROM lexic WHERE word = :word";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':word', $words['0']);
                $stmt->execute();
                $wordId = $stmt->fetchAll();

                if (!empty($tagId) && !empty($wordId) && !is_null($tagId['0']['id']) && !is_null($wordId['0']['id'])) {
                    $sql= "INSERT INTO dictionary_to_lexic(tag_id, word_id) VALUES (:tag, :word) ON DUPLICATE KEY UPDATE word_id = :word, tag_id= tag";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':tag', $tagId['0']['id']);
                    $stmt->bindParam(':word', $wordId['0']['id']);
                    $stmt->execute();
                }
            }
        }
        fclose($file);

        return new RedirectResponse($this->generateUrl('plan'));
    }

}
