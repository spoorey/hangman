<?php


/**
 * Created by PhpStorm
 * User: David Spörri
 * Date: 13082015
 * Time: 09:14
 */

namespace Application\Controller;


use Application\Form\WordForm;
use Application\InputFilter\WordInputFilter;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use SebastianBergmann\Comparator\ExceptionComparatorTest;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Word;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class WordController extends AbstractActionController implements ObjectManagerAwareInterface
{

    use ProvidesObjectManager;

    public function indexAction()
    {
        $dql = 'SELECT w FROM ' . Word::class . ' w ORDER BY w.word desc';

        $itemsPerPage = 50;

        $query = $this->getObjectManager()->createQuery($dql);
        $adapter = new DoctrineAdapter(new ORMPaginator($query));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(50);
        $page = (int) $this->params()->fromQuery('p');

        if ($page != null) {
            $paginator->setCurrentPageNumber($page);
        } else {
            $paginator->setCurrentPageNumber(1);
        }

        $pages = $paginator->getTotalItemCount() / $itemsPerPage;

        $paginator->setPageRange($pages);

        $viewModel = new ViewModel();

        $viewModel->setVariables([
            'words'       => $paginator->getCurrentItems(),
            'pageCount'       => $paginator->getPageRange() + 1,
            'currentPage' => $paginator->getCurrentPageNumber(),
        ]);
        foreach ($paginator->getCurrentItems() as $item) {
            //echo $item->getWord() . PHP_EOL;
        }

        return $viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $objectManager = $this->getObjectManager();
        $wordRepo = $objectManager->getRepository(Word::class);
        $word = $wordRepo->find((int) $id);
        if (!$word instanceof Word) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('error/404');
            $viewModel->setVariable('reason', 'Dieses Wort konnte nicht gefunden werden.');

            return $viewModel;
        }


        $form = new WordForm();
        $form->setData(['word' => $word->getWord()]);

        $conflict = false;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            $form->setInputFilter(new WordInputFilter());
            if ($form->isValid()) {
                $data = $form->getData();
                $form->setData($data);
                $existingWord = $this->getObjectManager()->getRepository(Word::class)->findOneBy(['word' => $data['word']]);
                if ($existingWord instanceof Word && $existingWord->getId() != $word->getId())  {
                    $conflict = true;
                } else {
                    $word->setWord($data['word']);
                    $this->getObjectManager()->persist($word);
                    $this->getObjectManager()->flush();
                }
            }
        }


        $viewModel = new ViewModel([
            'form' => $form,
            'conflict' => $conflict,
        ]);

        return $viewModel;
    }

    public function fillAction()
    {
        $wordRepo = $this->getObjectManager()->getRepository(Word::class);

        $words = [
            'Abendländischer Lebensbaum',
            'Afrikanischer Butterbaum',
            'Afrikanischer Mahagonibaum',
            'Afrikanischer Tulpenbaum',
            'Afzelia',
            'Ährige Felsenbirne',
            'Ährige Scheinhasel',
            'Aleppo-Kiefer',
            'Alexandrinischer Lorbeer',
            'Algerische Eiche',
            'Alpen-Goldregen',
            'Amerikanische Buche',
            'Amerikanische Eberesche',
            'Amerikanische Gleditschie',
            'Amerikanische Hainbuche',
            'Amerikanische Klettertrompete',
            'Amerikanische Linde',
            'Amerikanische Reif-Weide',
            'Amerikanischer Amberbaum',
            'Amerikanischer Erdbeerbaum',
            'Amerikanischer Schlangenhaut-Ahorn',
            'Amerikanisches Gelbholz',
            'Amur-Korkbaum',
            'Amur-Traubenkirsche',
            'Andentanne',
            'Aprikose',
            'Arganbaum',
            'Arizona-Zypresse',
            'Armblütige Scheinhasel',
            'Atlas-Zeder',
            'Australische Silbereiche',
            'Babylonische Trauer-Weide',
            'Balearen-Johanniskraut',
            'Balsam-Pappel',
            'Balsam-Tanne',
            'Banks-Kiefer',
            'Baum-Anemone',
            'Baum-Hasel',
            'Baum-Heide',
            'Beinwellblättrige Zistrose',
            'Berg-Ahorn',
            'Berg-Ahorn',
            'Berg-Kiefer, Berg-Föhre',
            'Berg-Kirsche',
            'Berg-Schneeglöckchenbaum',
            'Berg-Ulme',
            'Berg-Waldrebe',
            'Besen-Ginster',
            'Bibernell-Rose',
            'Billards Spierstrauch',
            'Birke',
            'Birke',
            'Birken-Pappel',
            'Birkenblättrige Birne',
            'Birkenfeige',
            'Bitternuss',
            'Bitterorange',
            'Bittersüßer Nachtschatten',
            'Blau-Tanne',
            'Blaubeere',
            'Blaue Atlas-Zeder',
            'Blaue Passionsblume',
            'Blaue Stech-Fichte',
            'Blauer Holunder',
            'Blaugrüner Tabak',
            'Blut-Ahorn',
            'Blut-Buche',
            'Blut-Johannisbeere',
            'Blut-Johanniskraut',
            'Blut-Pflaume',
            'Blüten-Hartriegel',
            'Blutroter Hartriegel',
            'Bodnant-Schneeball',
            'Bogen-Flieder',
            'Borsten-Fichte',
            'Borstige Robinie',
            'Borstiger Flügelstorax',
            'Bougainvillea',
            'Breitblättrige Lorbeerrose',
            'Breitblättrige Steinlinde',
            'Brennende Waldrebe',
            'Brombeere',
            'Bruch-Weide',
            'Buloke',
            'Buntblättrige Buche',
            'Bunter Strahlengriffel',
            'Burkwoods Duftblüte',
            'Busch-Eiche',
            'Butternuss',
            'Carolina-Rosskastanie',
            'Carolina-Schneeglöckchenbaum',
            'Cashew',
            'Cassia siberiana',
            'Catawba-Rhododendron',
            'Chenault-Schneebeere',
            'Chinesische Birne',
            'Chinesische Blaugurke',
            'Chinesische Blumenesche',
            'Chinesische Dattel',
            'Chinesische Flügelnuss',
            'Chinesische Hanfpalme',
            'Chinesische Kopfeibe',
            'Chinesische Pimpernuss',
            'Chinesische Spießtanne',
            'Chinesische Ulme',
            'Chinesische Winterblüte',
            'Chinesische Zierquitte',
            'Chinesischer Amberbaum',
            'Chinesischer Apfel',
            'Chinesischer Blauglockenbaum',
            'Chinesischer Blauregen, Glyzinie',
            'Chinesischer Gewürzstrauch',
            'Chinesischer Judasbaum',
            'Chinesischer Klebsame',
            'Chinesischer Rosen-Eibisch',
            'Chinesischer Schneeflockenstrauch',
            'Chinesisches Rotholz',
            'Christdorn',
            'Cissusblättriger Ahorn',
            'Colorado-Tanne',
            'Coulter-Kiefer',
            'Cunninghams Araukarie',
            'Cunninghams Kasuarine',
            'Dahurische Radspiere',
            'Davids Pfirsisch',
            'Davids Schneeball',
            'Davids-Ahorn',
            'Davids-Glanzmispel',
            'Davids-Kiefer',
            'Dicksons Ehretia',
            'Diels Zwergmispel',
            'Doldiger Flügel-Storax',
            'Doorenbos Weißrindige Himalaja-Birke',
            'Dornige Ölweide',
            'Douglasie',
            'Drachen-Weide',
            'Drachenbaum',
            'Dreh-Kiefer',
            'Dreiblütiger Ahorn',
            'Dreizähniger Ahorn',
            'Drummonds Spitz-Ahorn',
            'Duftender Schneeball',
            'Eberesche',
            'Echte Mehlbeere',
            'Echte Pavie',
            'Echte Pistazie',
            'Echte Quitte',
            'Echte Weinrebe',
            'Echter Feigenbaum',
            'Echter Gewürzstrauch',
            'Echter Hopfen',
            'Edel-Tanne',
            'Eichblatt-Hortensie',
            'Eichenblättrige Hainbuche',
            'Einblatt-Esche',
            'Eingriffeliger Weißdorn',
            'Eisenholzbaum',
            'Elsbeere',
            'Engelmanns Fichte',
            'Erlenblättrige Birke',
            'Erlenblättrige Mehlbeere',
            'Erlenblättrige Zimterle',
            'Erlenblättriger Schneeball',
            'Eschen-Ahorn',
            'Ess-Kastanie',
            'Essig-Baum',
            'Etruskisches Geißblatt',
            'Europäische Bleiwurz',
            'Europäische Lärche',
            'Europäische Zwergpalme',
            'Fächer-Ahorn',
            'Fächer-Zwergmispel',
            'Färber-Eiche',
            'Farnblättrige Buche',
            'Feld-Ahorn',
            'Feld-Ulme',
            'Felderbach-Buche',
            'Felsen-Ahorn',
            'Felsen-Gebirgstanne',
            'Felsen-Kirsche',
            'Felsen-Kreuzdorn',
            'Felsengebirgs-Wacholder',
            'Fenchelholzbaum',
            'Feuer-Ahorn',
            'Feuerdorn',
            'Fiederblatt-Weißdorn',
            'Filz-Rose',
            'Filzige Apfelbeere',
            'Fingerblättrige Akebie',
            'Fingerstrauch',
            'Flammenbaum',
            'Flatter-Ulme',
            'Flaum-Eiche',
            'Flockige Zwergmispel',
            'Flügel-Spindelstrauch',
            'Forsythie',
            'Frangipani',
            'Französische Hybrid-Säckelblume',
            'Fruchtbarer Gewürzstrauch',
            'Frühlings-Tamariske',
            'Fuji-Kirsche',
            'Garten-Hortensie',
            'Gefüllter Gewöhnlicher Schneeball',
            'Gefüllter Japanischer Schneeball',
            'Gelb-Birke',
            'Gelb-Kiefer',
            'Gelbe Rosskastanie',
            'Gelber Flammenbaum',
            'Gelber Sommerflieder',
            'Gelber Trompetenbaum',
            'Gelbrinden-Akazie',
            'Gemeine Buche',
            'Gemeine Eibe',
            'Gemeine Esche',
            'Gemeine Felsenbirne',
            'Gemeine Fichte',
            'Gemeine Hasel',
            'Gemeine Myrte',
            'Gemeine Rosskastanie',
            'Gemeine Schneebeere',
            'Gemeine Trauben-Kirsche',
            'Gemeiner Faulbaum',
            'Gemeiner Schneeball',
            'Gemeiner Wacholder',
            'Gemeines Pfaffenhütchen',
            'Gerippte Birke',
            'Geschwänzter Ahorn',
            'Geweihbaum',
            'Gewöhnliche Berberitze',
            'Gewöhnliche Mahonie',
            'Gewöhnliche Platane',
            'Gewöhnliche Waldrebe',
            'Gewöhnliche Zwergmispel',
            'Gewöhnlicher Bastardindigo',
            'Gewöhnlicher Blasenstrauch',
            'Gewöhnlicher Buchsbaum',
            'Gewöhnlicher Efeu',
            'Gewöhnlicher Erbsenstrauch',
            'Gewöhnlicher Eukalyptus',
            'Gewöhnlicher Flieder',
            'Gewöhnlicher Goldregen',
            'Gewöhnlicher Judasbaum',
            'Gewöhnlicher Liguster',
            'Gewöhnlicher Perückenstrauch',
            'Gewöhnlicher Seidelbast',
            'Gewöhnlicher Sommerflieder',
            'Gewürzstrauch',
            'Ghost Tree',
            'Ginkgobaum',
            'Glänzender Liguster',
            'Glatte Arizona-Zypresse',
            'Gold-Birke Hybride',
            'Gold-Johannisbeere',
            'Gold-Ulme',
            'Goldlärche',
            'Götterbaum',
            'Granatapfel',
            'Grannen-Kiefer',
            'Grau-Erle',
            'Grau-Pappel',
            'Grau-Weide',
            'Graue Kirschmandel',
            'Graue Zistrose',
            'Griechische Tanne',
            'Großblättrige Feige',
            'Großblütige Abelie',
            'Großblütige Weißdorn-Mispel',
            'Großer Federbuschstrauch',
            'Großfrüchtiges Pfaffenhütchen',
            'Grossers Ahorn',
            'Grün-Erle',
            'Gurken-Magnolie',
            'Guttaperchabaum',
            'Hafer-Pflaume',
            'Hahnenkamm-Sicheltanne',
            'Hainbuche',
            'Hainbuchenblättriger Ahorn',
            'Hänge-Birke, Weiß-Birke',
            'Hänge-Buche',
            'Hänge-Fichte',
            'Hänge-Silber-Linde',
            'Hängekätzchen-Weide',
            'Harlekin-Weide',
            'Harringtons Kopfeibe',
            'Heckrotts Geißblatt',
            'Henrys Heckenkirsche',
            'Henrys Linde',
            'Hiba-Lebensbaum',
            'Higan-Kirsche',
            'Himalaja-Fleischbeere',
            'Himalaja-Zeder',
            'Himalaja-Zeder ',
            'Himalaya-Baummispel',
            'Himbeere',
            'Himmelsbambus',
            'Höcker-Kiefer',
            'Holländische Linde',
            'Holz-Apfel',
            'Holz-Birne',
            'Holz-Quitte',
            'Hong-Kong-Orchideenbaum',
            'Honoki-Magnolie',
            'Hopfenbuche',
            'Hunds-Rose',
            'Hybrid-Lärche',
            'Hybrid-Zaubernuss',
            'Immerblühende Akazie',
            'Immergrüne Kriech-Heckenkirsche',
            'Immergrüne Magnolie',
            'Immergrüner Kreuzdorn',
            'Indianerbanane',
            'Indische Lagerstroemie',
            'Indische Rosskastanie',
            'Italienische Erle',
            'Italienische Waldrebe',
            'Italienische Zypresse',
            'Italienischer Ahorn',
            'Japanische Aprikose',
            'Japanische Blütenkirsche',
            'Japanische Felsenbirne',
            'Japanische Goldorange',
            'Japanische Hainbuche',
            'Japanische Hemlocktanne',
            'Japanische Kaiser-Eiche',
            'Japanische Kamelie',
            'Japanische Kastanie',
            'Japanische Kornelkirsche',
            'Japanische Lärche',
            'Japanische Lavendelheide',
            'Japanische Mandel-Kirsche',
            'Japanische Rosskastanie',
            'Japanische Skimmie',
            'Japanische Wollmispel',
            'Japanische Zelkove',
            'Japanische Zierquitte',
            'Japanischer Angelikabaum',
            'Japanischer Blumen-Hartriegel',
            'Japanischer Feuer-Ahorn',
            'Japanischer Papierbusch',
            'Japanischer Perlschweif',
            'Japanischer Sagopalmfarn',
            'Japanischer Schlitzahorn',
            'Japanischer Schneeball',
            'Japanischer Schnurbaum',
            'Japanischer Spierstrauch',
            'Japanischer Storaxbaum',
            'Jeffrey-Kiefer',
            'Jerusalemsdorn',
            'Johannisbrotbaum',
            'Julianes Berberitze',
            'Kahle Apfelbeere',
            'Kahle Felsenbirne',
            'Kahle Glanzmispel',
            'Kakipflaume',
            'Kalifornische Palme',
            'Kampferbaum',
            'Kamtschatka-Heckenkirsche',
            'Kanada-Pappel',
            'Kanadische Hemlock',
            'Kanadischer Judasbaum',
            'Kanadischer Schneeball',
            'Kanarische Dattelpalme',
            'Kanarische Kiefer',
            'Kapernstrauch',
            'Karambole',
            'Karminroter Zylinderputzer',
            'Karroo-Akazie',
            'Kastanienblättrige Eiche',
            'Kasuarine',
            'Katsurabaum',
            'Kaukasische Flügelnuss',
            'Kaukasische Linde',
            'Kaukasische Mandel',
            'Kaukasischer Faulbaum',
            'Kaukasus-Fichte',
            'Kermes-Eiche',
            'Kirschlorbeer',
            'Kirschpflaume',
            'Kiwi',
            'Kleeulme',
            'Kleinasiatische Tanne',
            'Klettenfrüchtige Eiche',
            'Kletter-Hortensie',
            'Kletternder Spindelstrauch',
            'Knopfstrauch',
            'Kobushi-Magnolie',
            'Kocks Bauhinie',
            'Kokospalme',
            'Kolchische Pimpernuss',
            'Kolchischer Ahorn',
            'Kolkwitzie',
            'Königsnuss',
            'Korb-Weide',
            'Korea-Kiefer',
            'Korea-Tanne',
            'Koreanische Berberitze',
            'Koreanischer Schlangenhaut-Ahorn',
            'Kork-Eiche',
            'Korkenzieher-Hasel',
            'Korkenzieher-Weide',
            'Kornelkirsche',
            'Korsische Kiefer',
            'Krause Zistrose',
            'Kretische Zistrose',
            'Kretischer Ahorn',
            'Krim-Linde',
            'Kugelblütiger Sommerflieder',
            'Kultur-Birne',
            'Kultur-Stachelbeere',
            'Kulturapfel',
            'Kumquat',
            'Kupfer-Felsenbirne',
            'Küsten-Mammutbaum',
            'Küsten-Tanne',
            'Lack-Zistrose',
            'Lamberts-Hasel',
            'Langblättrige Deutzie',
            'Lawsons Scheinzypresse',
            'Lederblatt-Ahorn',
            'Lederblättriger Weißdorn',
            'Leier-Feige',
            'Leuchtende Birke',
            'Libanon Zeder',
            'Libanon-Eiche',
            'Liebliche Weigelie',
            'Lindenblättrige Birke',
            'Lorbeerbaum',
            'Lorbeerblättrige Zistrose',
            'Lorbeerblättriger Schneeball',
            'Losbaum',
            'Lotuspflaume',
            'Maacks Heckenkirsche',
            'Macchien-Geißblatt',
            'Mädchen-Kiefer',
            'Mahagoni-Kirsche',
            'Mahonie',
            'Mandelbaum',
            'Mandelbäumchen',
            'Mandschurische Tanne',
            'Mandschurischer Ahorn',
            'Manilapalme',
            'Manna-Esche',
            'Mastixstrauch',
            'Maulbeer-Feige',
            'Mesquite',
            'Mexikanische Fächerpalme',
            'Mexikanische Orangenblume',
            'Mirabelle',
            'Mispel',
            'Mistel',
            'Mittelmeer-Seidelbast',
            'Mönchspfeffer',
            'Mongolische Birke',
            'Mongolische Linde',
            'Montpelier-Zistrose',
            'Moor-Birke',
            'Morgenländische Platane',
            'Morgenländischer Lebensbaum',
            'Myoporum serratum',
            'Myrtenblättrige Kreuzblume',
            'Nashi-Birne',
            'Nikko-Tanne',
            'Nootka-Scheinzypresse',
            'Nordmanns Tanne',
            'Norfolk-Hibiskus',
            'Norfolktanne',
            'Nutalls Blumen-Hartriegel',
            'Obassia-Storaxbaum',
            'Ohio-Rosskastanie',
            'Ohr-Weide',
            'Ölbaum, Olive',
            'Oleander',
            'Ölweide-Hybride',
            'Orange',
            'Oregon-Ahorn',
            'Oregon-Esche',
            'Orient-Buche',
            'Orientalischer Amberbaum',
            'Osagedorn',
            'Oster-Schneeball',
            'Palisanderholzbaum',
            'Panzer-Kiefer',
            'Papier-Birke',
            'Papiermaulbeerbaum',
            'Pappelblättrige Birke Hybride',
            'Pappelblättrige Zistrose',
            'Pappelblättriger Brachychiton',
            'Paradiesvogelbusch',
            'Paternosterbaum',
            'Pekannuss',
            'Persische Eiche',
            'Petterie',
            'Pfefferbaum',
            'Pfeifenstrauch',
            'Pfeifenstrauch, Virginalis-Gruppe',
            'Pfirsich',
            'Pflaume',
            'Pflaumenblättrige Apfelbeere',
            'Pfriemenginster',
            'Pimpernuss',
            'Pinie',
            'Platanenblättrige Alangie',
            'Platanenblättriger Maulbeerbaum',
            'Pontische Eiche',
            'Portugiesische Eiche',
            'Portugiesische Lorbeerkirsche',
            'Pracht-Spierstrauch',
            'Prächtige Lagerstroemie',
            'Prächtiger Trompetenbaum',
            'Preißelbeere',
            'Purgier-Kreuzdorn',
            'Purpur-Apfel',
            'Purpur-Magnolie',
            'Purpur-Schönfrucht',
            'Purpur-Weide',
            'Purpurblättriger Trompetenbaum',
            'Purpus Heckenkirsche',
            'Pyramiden-Pappel',
            'Pyrenäen-Eiche',
            'Pyrenäen-Kiefer',
            'Queensland Strahlenaralie',
            'Queensland-Araukarie',
            'Queensland-Flaschenbaum',
            'Rain Tree',
            'Ranunkelstrauch',
            'Raublättrige Deutzie',
            'Rauchzypresse',
            'Rauchzypresse, Flusszeder',
            'Raue Stechwinde',
            'Rauhe Hortensie',
            'Rauschbeere',
            'Rautenblättrige Stechpalme',
            'Reneklode',
            'Riesen-Hartriegel',
            'Riesen-Lebensbaum',
            'Riesen-Mammutbaum',
            'Rispen-Hartriegel',
            'Rispen-Hortensie',
            'Rispiger Blasenbaum',
            'Robinia x margaretta',
            'Robinie',
            'Rosen-Deutzie',
            'Rosen-Kaktus',
            'Rosmarinheide',
            'Rosskastanien-Hybrid',
            'Rostnerviger Schlangenhaut-Ahorn',
            'Rostrote Weinrebe',
            'Rot-Ahorn',
            'Rot-Eiche',
            'Rotbeeriger Wacholder',
            'Rotblättriger Berg-Ahorn',
            'Rotblühende Rosskastanie',
            'Rotdorn',
            'Rote Heckenkirsche',
            'Rote Johannisbeere',
            'Rumelische Kiefer',
            'Runzelblättriger Schneeball',
            'Sadebaum',
            'Saft-Weißdorn',
            'Sal-Weide',
            'Salbeiblättrige Zistrose',
            'Salzstrauch',
            'Samthaarige Stinkesche',
            'Sandbüchsenbaum',
            'Sanddorn',
            'Sargents Apfel',
            'Sargents Samt-Hortensie',
            'Sauerbaum',
            'Sauerkirsche',
            'Säulen-Araukarie',
            'Säulen-Stiel-Eiche',
            'Sawara-Scheinzypresse',
            'Scharlach-Eiche',
            'Scharlach-Weißdorn',
            'Schaumspiere',
            'Scheinkamelie',
            'Scheinkerrie',
            'Scheinparrotie',
            'Schindel-Eiche',
            'Schirm-Magnolie',
            'Schirm-Ölweide',
            'Schirmtanne',
            'Schlangenhaut-Kiefer',
            'Schlehe',
            'Schlitzblättrige Hänge-Birke',
            'Schmalblättrige Esche',
            'Schmalblättrige Ölweide',
            'Schmalblättrige Steinlinde',
            'Schmalblättriger Lavendel',
            'Schneeballblättrige Blasenspiere',
            'Schneeballblättriger Ahorn',
            'Schöne Lycesterie',
            'Schopf-Lavendel',
            'Schuppenrinden-Hickorynuss',
            'Schwarz-Birke',
            'Schwarz-Eiche',
            'Schwarz-Erle',
            'Schwarz-Fichte',
            'Schwarz-Kiefer',
            'Schwarz-Pappel',
            'Schwarze Heckenkirsche',
            'Schwarzer Holunder',
            'Schwarzer Maulbeerbaum',
            'Schwarzfrüchtiger Weißdorn',
            'Schwarznuss',
            'Schwedische Mehlbeere',
            'Seemandel',
            'Seetraube',
            'Seidenbaum',
            'Seidenraupen-Eiche',
            'Serbische Fichte',
            'Shumard-Eiche',
            'Sibirische Aprikose',
            'Sibirische Fiederspiere',
            'Sibirische Ulme',
            'Sicheltanne',
            'Sieben Söhne des Himmels Strauch',
            'Siebolds Fingeraralie',
            'Silber-Ahorn',
            'Silber-Akazie',
            'Silber-Büffelbeere',
            'Silber-Linde',
            'Silber-Pappel',
            'Silber-Weide',
            'Silberginster',
            'Siskiyou-Fichte',
            'Sitka-Erle',
            'Sitka-Fichte',
            'Sommer-Linde',
            'Sommer-Magnolie',
            'Sonnenschirmbaum',
            'Spanische Eiche',
            'Spanische Tanne',
            'Späte Traubenkirsche',
            'Spätsommer-Duftblüte',
            'Speierling',
            'Spitz-Ahorn',
            'Spottnuss-Hickory',
            'Stacheliger Dornginster',
            'Stacheliger Mäusedorn',
            'Stechender Spargel',
            'Stechginster',
            'Stechpalme',
            'Stein-Eiche',
            'Sternmagnolie',
            'Stiel-Eiche',
            'Stiel-Eiche',
            'Stinkstrauch',
            'Strand-Kiefer',
            'Strauch-Eibisch',
            'Strauch-Päonie',
            'Strauch-Rosskastanie',
            'Südbuche',
            'Südlicher Zürgelbaum',
            'Sumpf-Eiche',
            'Sumpf-Porst',
            'Sumpfzypresse',
            'Süntel-Buche',
            'Surenbaum',
            'Taiwanie',
            'Tartarischer Hartriegel',
            'Taschentuchbaum',
            'Tataren-Ahorn',
            'Tataren-Heckenkirsche',
            'Täuschendes Gelbholz',
            'Tempel-Kiefer',
            'Terpentin-Pistazie',
            'Thunbergs Berberitze',
            'Thunbergs Kiefer',
            'Thunbergs Spierstrauch',
            'Tigerschwanz-Fichte',
            'Tipubaum',
            'Tränen-Kiefer',
            'Trauben-Eiche',
            'Trauben-Eiche',
            'Trauben-Holunder',
            'Trauer-Weide',
            'Trompetenbaum',
            'Tulpen-Magnolie',
            'Tulpenbaum',
            'Ulme "Jacqueline Hillier"',
            'Ungarische Eiche',
            'Üppige Robinie',
            'Ussuri-Spindelstrauch',
            'Veitchs Scheinhasel',
            'Veitchs Tanne',
            'Vielblütige Lavendelheide',
            'Vielblütige Zwergmispel',
            'Vielblütiger Apfel',
            'Vielblütiges Doppelschild',
            'Virginische Hopfenbuche',
            'Virginische Zaubernuss',
            'Virginischer Schneeflockenstrauch',
            'Vogel-Kirsche',
            'Wald-Geißblatt',
            'Wald-Hortensie',
            'Wald-Kiefer, Wald-Föhre',
            'Wald-Tupelobaum',
            'Walnuss',
            'Wandelröschen',
            'Weidenblättrige Birne',
            'Weihnachtsbaum, Christbaum',
            'Weinblatt-Ahorn',
            'Weiß-Eiche',
            'Weiß-Fichte',
            'Weiß-Tanne',
            'Weißer Maulbeerbaum',
            'Weißliche Zistrose',
            'Westamerikanische Weymouths-Kiefer',
            'Westliche Hemlockstanne',
            'Westlicher Erdbeerbaum',
            'Westlicher Zürgelbaum',
            'Weymouths Kiefer',
            'Wiesners Magnolie',
            'Wilder Wein',
            'Wilder Wein',
            'Wilsons Fichte',
            'Winter-Jasmin',
            'Winter-Linde',
            'Wintergrüne Eiche',
            'Wintergrüner Liguster',
            'Wollemie',
            'Wolliger Schneeball',
            'Wunderbaum',
            'Yulan-Magnolie',
            'Zapfennuss',
            'Zerr-Eiche',
            'Zimmeraralie',
            'Zimt-Ahorn',
            'Zirbel-Kiefer',
            'Zitrone',
            'Zitter-Pappel',
            'Zoescheners Ahorn',
            'Zucker-Ahorn',
            'Zucker-Birke',
            'Zuckerhut-Fichte',
            'Zweigriffeliger Weißdorn',
            'Zweihäusige Kermesbeere',
            'Zwetschge',
            'Zypern-Zeder',
            'Alphabet',
            'Altenheim',

            'Amulett',
            'Anlage',

            'Arm',
            'Aufkleber',

            'Auspuff',
            'Auto',

            'Ball',
            'Bar',

            'Baum',
            'Bestellliste',

            'Betttuch',
            'Biokraftstoff',

            'Blatt',
            'Buch',

            'Callcenter',
            'Castingshow',

            'Chinese',
            'Clip',

            'Computer',
            'Dach',

            'Dichtung',
            'Disco',

            'Dollar',
            'Dorfschule',

            'Eimer',
            'Eisenbahn',

            'Engel',
            'Erdöl',

            'Ergebnis',
            'Fahrrad',

            'Feuerlöscher',
            'Film',

            'Foto',
            'Freiheit',

            'Gehirn',
            'Gehweg',

            'Grundgesetz',
            'Grundstück',

            'Gymnasium',
            'Hafen',

            'Haus',
            'Heimatland',

            'Holz',
            'Horn',

            'Igel',
            'Impfstoff',

            'Information',
            'Infusion',

            'Insel',
            'Jachthafen',

            'Jacke',
            'Jäger',

            'Jobcenter',
            'Jugendclub',

            'Kaktus',
            'Kamm',

            'Kammer',
            'Keller',

            'Kugel',
            'Leber',

            'Leiste',
            'Leiter',

            'Liebe',
            'Locher',

            'Maus',
            'Monat',

            'Monitor',
            'Musikstück',

            'Muskel',
            'Nabelschnur',

            'Nachbar',
            'Nagel',

            'Nase',
            'Natur',

            'Nonne',
            'Notunterkunft',

            'Obst',
            'Ochse',

            'Offizier',
            'Orgel',

            'Osterei',
            'Paket',

            'Papier',
            'Passwort',

            'Politiker',
            'Poster',

            'Quader',
            'Quark',

            'Quecksilber',
            'Quelle',

            'Quastenflosser',
            'Rabe',

            'Radio',
            'Rakete',

            'Reifen',
            'Rettungswagen',

            'Ritter',
            'Sand',

            'Scanner',
            'Schloss',

            'Stein',
            'Strauch',

            'Tasche',
            'Taschenrechner',

            'Tastatur',
            'Taste',

            'Tiger',
            'Tisch',

            'Turnschuh',
            'Uhr',

            'Ulme',
            'Umschlagplatz',

            'Umwelt',
            'Unwetter',

            'Vanille',
            'Vater',

            'Verdauung',
            'Verkehr',

            'Versicherung',
            'Vogel',

            'Waage',
            'Waggon',

            'Waschzeug',
            'Wasser',

            'Wort',
            'Xylophon',

            'Yogalehrer',
            'Zahn',

            'Zeichen',
            'Zeitung',

            'Zentrum',
        ];

        foreach ($words as $word) {
            if ($wordRepo->findOneBy(['word' => $word]) instanceof Word) {
                if ($_GET['force'] == '1') {
                    do {
                        $word .= 'a';
                    } while ($wordRepo->findOneBy(['word' => $word]) instanceof Word);
                } else {
                    continue;
                }
            }
            echo $word . '<br>' . PHP_EOL;


            $entity = new Word();
            $entity->setWord($word);
            $this->getObjectManager()->persist($entity);
            $this->getObjectManager()->flush();
        }

        die();
    }

    public function addAction(){
        $form = new WordForm();

        $request = $this->getRequest();
        $conflict = false;
        if ($request->isPost()) {
            $form->setData($request->getPost());
            $form->setInputFilter(new WordInputFilter());
            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->getObjectManager()->getRepository(Word::class)->findOneBy(['word' => $data['word']]))  {
                    $conflict = true;
                } else {
                    $word = new Word();
                    $word->setWord($data['word']);
                    $this->getObjectManager()->persist($word);
                    $this->getObjectManager()->flush();
                    $this->redirect()->toRoute('application/words');
                }
            }
        }

        $viewModel = new ViewModel([
            'form' => $form,
            'conflict' => $conflict,

        ]);

        return $viewModel;
    }

    static function getEditForm(Word $word) {
        $form = new WordForm();
        $form->setData(['word' => $word->getWord()]);
        return $form;
    }
}

