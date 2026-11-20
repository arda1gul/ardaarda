<?php

namespace App\Controller;

use App\Entity\Score;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class QuizController extends AbstractController
{
    // Uitgebreide databank met 6 categorieën, elk 3 varianten van 20 vragen
private array $categories = [
        'algemene_kennis' => [
            'title' => 'Algemene Kennis',
            'description' => 'Brede vragen over wetenschap, natuur en aardrijkskunde.',
            'icon' => 'bi-lightbulb-fill',
            'color' => 'text-warning',
            'variants' => [
                1 => [
                    'title' => 'Variant 1: Basis & Wetenschap',
                    'questions' => [
                        ['id' => 1, 'question' => 'Hoeveel planeten telt ons zonnestelsel?', 'options' => ['7', '8', '9', '10'], 'correct' => '8'],
                        ['id' => 2, 'question' => 'Welk chemisch element heeft het symbool "O"?', 'options' => ['Goud', 'Zuurstof', 'Zilver', 'Ijzer'], 'correct' => 'Zuurstof'],
                        ['id' => 3, 'question' => 'Wat is het grootste zoogdier ter wereld?', 'options' => ['Afrikaanse olifant', 'Blauwe vinvis', 'Potvis', 'Giraf'], 'correct' => 'Blauwe vinvis'],
                        ['id' => 4, 'question' => 'Hoeveel botten heeft een volwassen menselijk lichaam?', 'options' => ['180', '206', '250', '300'], 'correct' => '206'],
                        ['id' => 5, 'question' => 'Welke planeet staat het dichtst bij de zon?', 'options' => ['Venus', 'Mars', 'Mercurius', 'Aarde'], 'correct' => 'Mercurius'],
                        ['id' => 6, 'question' => 'Wat is de snelheid van het licht in vacuüm (ongeveer)?', 'options' => ['300.000 km/s', '150.000 km/s', '1.000.000 km/s', '30.000 km/s'], 'correct' => '300.000 km/s'],
                        ['id' => 7, 'question' => 'Welk gas komt het meeste voor in de aardatmosfeer?', 'options' => ['Zuurstof', 'Stikstof', 'Koolstofdioxide', 'Waterstof'], 'correct' => 'Stikstof'],
                        ['id' => 8, 'question' => 'Wie schreef het boek "De Kleine Prins"?', 'options' => ['J.K. Rowling', 'Antoine de Saint-Exupéry', 'Roald Dahl', 'J.R.R. Tolkien'], 'correct' => 'Antoine de Saint-Exupéry'],
                        ['id' => 9, 'question' => 'Wat is de hoofdstad van Australië?', 'options' => ['Sydney', 'Melbourne', 'Canberra', 'Brisbane'], 'correct' => 'Canberra'],
                        ['id' => 10, 'question' => 'Welk instrument meet luchtdruk?', 'options' => ['Thermometer', 'Barometer', 'Hygrometer', 'Anemometer'], 'correct' => 'Barometer'],
                        ['id' => 11, 'question' => 'Hoeveel tanden heeft een volwassen mens gemiddeld (inclusief verstandskiezen)?', 'options' => ['28', '30', '32', '34'], 'correct' => '32'],
                        ['id' => 12, 'question' => 'Welke oceaan is de grootste?', 'options' => ['Atlantische Oceaan', 'Indische Oceaan', 'Stille Oceaan (Grote Oceaan)', 'Noordelijke IJszee'], 'correct' => 'Stille Oceaan (Grote Oceaan)'],
                        ['id' => 13, 'question' => 'Wat is de hardste natuurlijke stof op aarde?', 'options' => ['Goud', 'Ijzer', 'Diamant', 'Graniet'], 'correct' => 'Diamant'],
                        ['id' => 14, 'question' => 'In welk jaar vond de eerste maanlanding plaats?', 'options' => ['1965', '1969', '1971', '1975'], 'correct' => '1969'],
                        ['id' => 15, 'question' => 'Welke kleur krijg je als je blauw en geel mengt?', 'options' => ['Paars', 'Groen', 'Oranje', 'Bruin'], 'correct' => 'Groen'],
                        ['id' => 16, 'question' => 'Wat is de formule voor water?', 'options' => ['CO2', 'H2O', 'NaCl', 'O3'], 'correct' => 'H2O'],
                        ['id' => 17, 'question' => 'Welk continent heeft de meeste inwoners?', 'options' => ['Afrika', 'Europa', 'Azië', 'Noord-Amerika'], 'correct' => 'Azië'],
                        ['id' => 18, 'question' => 'Hoeveel continenten telt de aarde?', 'options' => ['5', '6', '7', '8'], 'correct' => '7'],
                        ['id' => 19, 'question' => 'Wie schilderde de Mona Lisa?', 'options' => ['Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Rembrandt van Rijn'], 'correct' => 'Leonardo da Vinci'],
                        ['id' => 20, 'question' => 'Wat is de grootste woestijn ter wereld?', 'options' => ['Sahara', 'Gobi', 'Antarctische Woestijn', 'Arabische Woestijn'], 'correct' => 'Antarctische Woestijn']
                    ]
                ],
                2 => [
                    'title' => 'Variant 2: Natuur & Cultuur',
                    'questions' => [
                        ['id' => 1, 'question' => 'Welk dier staat bekend als de koning der dieren?', 'options' => ['Tijger', 'Leeuw', 'Olifant', 'Beer'], 'correct' => 'Leeuw'],
                        ['id' => 2, 'question' => 'Wat is de nationale sport van Japan?', 'options' => ['Karate', 'Sumoworstelen', 'Honkbal', 'Judo'], 'correct' => 'Sumoworstelen'],
                        ['id' => 3, 'question' => 'Welk land staat bekend om duizenden meren en de sauna?', 'options' => ['Noorwegen', 'Zweden', 'Finland', 'Denemarken'], 'correct' => 'Finland'],
                        ['id' => 4, 'question' => 'Hoeveel snaren heeft een standaardaikoestische gitaar?', 'options' => ['4', '5', '6', '8'], 'correct' => '6'],
                        ['id' => 5, 'question' => 'Welk monument in Parijs werd gebouwd voor de Wereldtentoonstelling van 1889?', 'options' => ['Triomfboog', 'Eiffeltoren', 'Notre-Dame', 'Louvre'], 'correct' => 'Eiffeltoren'],
                        ['id' => 6, 'question' => 'Wat is de diepste oceanografische trog ter wereld?', 'options' => ['Puerto Ricotrog', 'Marianentrog', 'Java-trog', 'Philippinentrog'], 'correct' => 'Marianentrog'],
                        ['id' => 7, 'question' => 'Welke boomsoort produceert eikels?', 'options' => ['Beuk', 'Eik', 'Kastanje', 'Den'], 'correct' => 'Eik'],
                        ['id' => 8, 'question' => 'Wat is de grootste eilandstaat ter wereld naar landoppervlakte?', 'options' => ['Japan', 'Madagaskar', 'Groenland', 'Indonesië'], 'correct' => 'Groenland'],
                        ['id' => 9, 'question' => 'Welke kunststroming wordt geassocieerd met Salvador Dalí en smeltende horloges?', 'options' => ['Kubisme', 'Surrealisme', 'Impressionisme', 'Barok'], 'correct' => 'Surrealisme'],
                        ['id' => 10, 'question' => 'Welk element heeft het chemischesymbool "Fe"?', 'options' => ['Fluor', 'Fosfor', 'Ijzer', 'Ferdinandium'], 'correct' => 'Ijzer'],
                        ['id' => 11, 'question' => 'Hoeveel tanden heeft een volwassen haai in zijn leven?', 'options' => ['Precies 100', 'Duizenden (ze worden continu vervangen)', 'Ongeveer 32', 'Alleen bij de geboorte'], 'correct' => 'Duizenden (ze worden continu vervangen)'],
                        ['id' => 12, 'question' => 'In welk land ligt de stad Timbuktu?', 'options' => ['Mali', 'Egypte', 'Marokko', 'Nigeria'], 'correct' => 'Mali'],
                        ['id' => 13, 'question' => 'Welke taal wordt er gesproken in Brazilië?', 'options' => ['Spaans', 'Portugees', 'Frans', 'Engels'], 'correct' => 'Portugees'],
                        ['id' => 14, 'question' => 'Wat is de hoofdstad van Italië?', 'options' => ['Milaan', 'Venetië', 'Rome', 'Napels'], 'correct' => 'Rome'],
                        ['id' => 15, 'question' => 'Welke planeet staat bekend als de Rode Planeet?', 'options' => ['Jupiter', 'Mars', 'Saturnus', 'Venus'], 'correct' => 'Mars'],
                        ['id' => 16, 'question' => 'Wat is de grootste vlinder ter wereld (koningin Alexandra\'s vogelvlinder)?', 'options' => ['Nachtvlinder', 'Papoea-vlinder', 'Zuid-Amerikaanse Morpho', 'Monarchvlinder'], 'correct' => 'Papoea-vlinder'],
                        ['id' => 17, 'question' => 'Welk land heeft de meeste tijdzones (inclusief overzeese gebieden)?', 'options' => ['Rusland', 'Verenigde Staten', 'Frankrijk', 'China'], 'correct' => 'Frankrijk'],
                        ['id' => 18, 'question' => 'Wie componeerde de 9e Symfonie met \'Ode an die Freude\'?', 'options' => ['Mozart', 'Bach', 'Beethoven', 'Chopin'], 'correct' => 'Beethoven'],
                        ['id' => 19, 'question' => 'Wat is het kleinste land ter wereld qua oppervlakte?', 'options' => ['Monaco', 'San Marino', 'Vaticaanstad', 'Liechtenstein'], 'correct' => 'Vaticaanstad'],
                        ['id' => 20, 'question' => 'Welk dier kan als enige zoogdier echt vliegen (niet zweven)?', 'options' => ['Eekhoorn', 'Vleermuis', 'Vliegende hond', 'Koeskus'], 'correct' => 'Vleermuis']
                    ]
                ],
                3 => [
                    'title' => 'Variant 3: Wereldfeiten',
                    'questions' => [
                        ['id' => 1, 'question' => 'Welk land produceert de meeste koffie ter wereld?', 'options' => ['Colombia', 'Vietnam', 'Brazilië', 'Ethiopië'], 'correct' => 'Brazilië'],
                        ['id' => 2, 'question' => 'Wat is het land met de meeste inwoners ter wereld (recente jaren)?', 'options' => ['China', 'India', 'Verenigde Staten', 'Indonesië'], 'correct' => 'India'],
                        ['id' => 3, 'question' => 'Welke rivier stroomt door de Egyptische hoofdstad Caïro?', 'options' => ['Amazone', 'Nijl', 'Mekong', 'Eufraat'], 'correct' => 'Nijl'],
                        ['id' => 4, 'question' => 'Hoeveel toetsen heeft een standaard piano?', 'options' => ['68', '76', '88', '96'], 'correct' => '88'],
                        ['id' => 5, 'question' => 'Welke berg is de hoogste ter wereld boven zeeniveau?', 'options' => ['K2', 'Mount Everest', 'Kangchenjunga', 'Mont Blanc'], 'correct' => 'Mount Everest'],
                        ['id' => 6, 'question' => 'Wat is de munteenheid van het Verenigd Koninkrijk?', 'options' => ['Euro', 'Dollar', 'Pond Sterling', 'Frank'], 'correct' => 'Pond Sterling'],
                        ['id' => 7, 'question' => 'Welk sterrenbeeld bevat de Noordster (Polaris)?', 'options' => ['Grote Beer', 'Kleine Beer', 'Orion', 'Cassiopeia'], 'correct' => 'Kleine Beer'],
                        ['id' => 8, 'question' => 'In welk jaar werd het World Wide Web (WWW) uitgedacht door Tim Berners-Lee?', 'options' => ['1975', '1983', '1989', '1995'], 'correct' => '1989'],
                        ['id' => 9, 'question' => 'Welke chemische stof wordt gebruikt in gewone keukenzout?', 'options' => ['Natriumchloride', 'Kaliumnitraat', 'Waterstofperoxide', 'Calciumcarbonaat'], 'correct' => 'Natriumchloride'],
                        ['id' => 10, 'question' => 'Hoe heet de dikste en langste bot in het menselijk lichaam?', 'options' => ['Opperarmbeen', 'Schedel', 'Dijbeen (Femur)', 'Scheenbeen'], 'correct' => 'Dijbeen (Femur)'],
                        ['id' => 11, 'question' => 'Welke planeet heeft de meeste manen in ons zonnestelsel?', 'options' => ['Mars', 'Jupiter', 'Saturnus', 'Neptunus'], 'correct' => 'Saturnus'],
                        ['id' => 12, 'question' => 'Wat is de nationale bloem van Nederland?', 'options' => ['Roos', 'Tulp', 'Zonnebloem', 'Hyacint'], 'correct' => 'Tulp'],
                        ['id' => 13, 'question' => 'Welk continent heeft geen actieve vulkanen?', 'options' => ['Europa', 'Australië', 'Antarctica', 'Noord-Amerika'], 'correct' => 'Australië'],
                        ['id' => 14, 'question' => 'Hoeveel seconden zitten er in een hele dag (24 uur)?', 'options' => ['36.000', '86.400', '100.000', '144.000'], 'correct' => '86.400'],
                        ['id' => 15, 'question' => 'Welke stad wordt "The Big Apple" genoemd?', 'options' => ['Los Angeles', 'Chicago', 'New York', 'San Francisco'], 'correct' => 'New York'],
                        ['id' => 16, 'question' => 'Welk dier is het snelste landdier ter wereld?', 'options' => ['Leeuw', 'Antilope', 'Jachtluipaard (Cheetah)', 'Greyhound'], 'correct' => 'Jachtluipaard (Cheetah)'],
                        ['id' => 17, 'question' => 'Wat is de oudste nog bestaande universiteit ter wereld (gevestigd in Marokko)?', 'options' => ['Universiteit van Bologna', 'Universiteit van Al-Qarawiyyin', 'Universiteit van Oxford', 'Universiteit van Parijs'], 'correct' => 'Universiteit van Al-Qarawiyyin'],
                        ['id' => 18, 'question' => 'Welke kleur heeft een robijn van hoge kwaliteit?', 'options' => ['Blauw', 'Groen', 'Rood', 'Geel'], 'correct' => 'Rood'],
                        ['id' => 19, 'question' => 'Wat is het belangrijkste gas waarvan sterren zoals onze zon energie maken?', 'options' => ['Zuurstof', 'Waterstof', 'Helium', 'Stikstof'], 'correct' => 'Waterstof'],
                        ['id' => 20, 'question' => 'Hoeveel graden is de som van alle hoeken in een driehoek?', 'options' => ['90', '180', '270', '360'], 'correct' => '180']
                    ]
                ]
            ]
        ],
        'geschiedenis_turkije' => [
            'title' => 'Geschiedenis van Turkije',
            'description' => 'Van het Ottomaanse Rijk tot de oprichting van de moderne Republiek.',
            'icon' => 'bi-bank',
            'color' => 'text-danger',
            'variants' => [
                1 => [
                    'title' => 'Variant 1: Ottomaanse Rijk & Groei',
                    'questions' => [
                        ['id' => 1, 'question' => 'In welk jaar werd Constantinopel veroverd door Sultan Mehmed II?', 'options' => ['1299', '1453', '1517', '1923'], 'correct' => '1453'],
                        ['id' => 2, 'question' => 'Wie wordt beschouwd als de stichter van het Ottomaanse Rijk?', 'options' => ['Osman I', 'Orhan Gazi', 'Murat I', 'Bayezid I'], 'correct' => 'Osman I'],
                        ['id' => 3, 'question' => 'Welke sultan regeerde tijdens de hoogtebloei van het rijk in de 16e eeuw?', 'options' => ['Suleiman de Grote', 'Selim I', 'Mehmed de Veroveraar', 'Abdulhamid II'], 'correct' => 'Suleiman de Grote'],
                        ['id' => 4, 'question' => 'Wat was de eerste officiële hoofdstad van het Ottomaanse Rijk vóór Edirne en Istanbul?', 'options' => ['Bursa', 'Ankara', 'Konya', 'Iznik'], 'correct' => 'Bursa'],
                        ['id' => 5, 'question' => 'In welk jaar werd de Republiek Turkije uitgeroepen?', 'options' => ['1920', '1922', '1923', '1924'], 'correct' => '1923'],
                        ['id' => 6, 'question' => 'Wie was de eerste president van de Republiek Turkije?', 'options' => ['İsmet İnönü', 'Mustafa Kemal Atatürk', 'Celâl Bayar', 'Adnan Menderes'], 'correct' => 'Mustafa Kemal Atatürk'],
                        ['id' => 7, 'question' => 'Hoe heette het verdrag uit 1923 dat de moderne grenzen van Turkije vastlegde?', 'options' => ['Verdrag van Sèvres', 'Verdrag van Lausanne', 'Verdrag van Parijs', 'Verdrag van Berlijn'], 'correct' => 'Verdrag van Lausanne'],
                        ['id' => 8, 'question' => 'Welke stad werd in 1923 de nieuwe hoofdstad van Turkije?', 'options' => ['Istanbul', 'Izmir', 'Ankara', 'Bursa'], 'correct' => 'Ankara'],
                        ['id' => 9, 'question' => 'In welk jaar werd het Ottomaanse sultanaat afgeschaft?', 'options' => ['1920', '1922', '1923', '1924'], 'correct' => '1922'],
                        ['id' => 10, 'question' => 'Welke Slag in 1071 opende de deuren van Anatolië voor de Seltsjoeken?', 'options' => ['Slag bij Dardanellen', 'Slag bij Manzikert', 'Slag bij Mohács', 'Slag bij Lepanto'], 'correct' => 'Slag bij Manzikert'],
                        ['id' => 11, 'question' => 'Welke sultan kreeg de bijnaam "Yavuz" (de Streng/Grimmig)?', 'options' => ['Selim I', 'Bayezid II', 'Mehmed II', 'Ahmed I'], 'correct' => 'Selim I'],
                        ['id' => 12, 'question' => 'Wat was de naam van de elite-infanterieeenheid van het Ottomaanse leger?', 'options' => ['Spahis', 'Janitsaren (Yeniçeri)', 'Akıncılar', 'Azaps'], 'correct' => 'Janitsaren (Yeniçeri)'],
                        ['id' => 13, 'question' => 'In welk jaar werd het kalifaat definitief beëindigd?', 'options' => ['1922', '1924', '1926', '1928'], 'correct' => '1924'],
                        ['id' => 14, 'question' => 'Welke hervorming voerde in 1928 het Latijnse alfabet in?', 'options' => ['Taalrevolutie', 'Kledingrevolutie', 'Schrifthervorming', 'Grondwetswijziging'], 'correct' => 'Schrifthervorming'],
                        ['id' => 15, 'question' => 'Wie was de trouwe rechterhand van Atatürk en de tweede president?', 'options' => ['Fevzi Çakmak', 'İsmet İnönü', 'Kazım Karabekir', 'Celâl Bayar'], 'correct' => 'İsmet İnönü'],
                        ['id' => 16, 'question' => 'Welke Slag in 1444 betekende een grote Ottomaanse overwinning tegen de kruisvaarders?', 'options' => ['Slag bij Varna', 'Slag bij Kosovo', 'Slag bij Nikopolis', 'Slag bij Mohács'], 'correct' => 'Slag bij Varna'],
                        ['id' => 17, 'question' => 'Welke straat in Istanbul verbindt de Zwarte Zee met de Zee van Marmara?', 'options' => ['Dardanellen', 'Bosporus', 'Gouden Hoorn', 'Izmit-golf'], 'correct' => 'Bosporus'],
                        ['id' => 18, 'question' => 'In welk jaar verkreeg Turkije vrouwenstemrecht bij nationale verkiezingen?', 'options' => ['1923', '1930', '1934', '1945'], 'correct' => '1934'],
                        ['id' => 19, 'question' => 'Welke sultan liet de Blauwe Moskee (Sultan Ahmet-moskee) bouwen?', 'options' => ['Suleiman I', 'Ahmed I', 'Murat IV', 'Mehmed IV'], 'correct' => 'Ahmed I'],
                        ['id' => 20, 'question' => 'Hoe wordt de bevrijdingsoorlog (1919-1922) in de Turkse geschiedenis genoemd?', 'options' => ['Kurtuluş Savaşı', 'Çanakkale Savaşı', 'İstiklal Harbi', 'Beide A en C'], 'correct' => 'Beide A en C']
                    ]
                ],
                2 => [
                    'title' => 'Variant 2: Republiek & Hervormingen',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is de betekenis van de achternaam "Atatürk" die Mustafa Kemal ontving?', 'options' => ['Vader van de Turken', 'Grote Leider', 'Eerste Burger', 'Held van Ankara'], 'correct' => 'Vader van de Turken'],
                        ['id' => 2, 'question' => 'In welke stad werd Mustafa Kemal Atatürk in 1881 geboren?', 'options' => ['Istanbul', 'Salonica (Selanik)', 'Ankara', 'Izmir'], 'correct' => 'Salonica (Selanik)'],
                        ['id' => 3, 'question' => 'Welke slagoslag in 1915 tijdens de Eerste Wereldoorlog werd een legendarische Turkse verdedigingsoverwinning?', 'options' => ['Slag bij Gallipoli (Çanakkale)', 'Slag bij Sarıkamış', 'Slag bij Kut', 'Slag bij Gaza'], 'correct' => 'Slag bij Gallipoli (Çanakkale)'],
                        ['id' => 4, 'question' => 'Op welke datum wordt de Dag van de Overwinning (Zafer Bayramı) gevierd ter herdenking van de Slag bij Dumlupınar?', 'options' => ['23 april', '19 mei', '30 augustus', '29 oktober'], 'correct' => '30 augustus'],
                        ['id' => 5, 'question' => 'Op welke datum wordt de nationale feestdag van de Republiek Turkije (Cumhuriyet Bayramı) gevierd?', 'options' => ['29 oktober', '30 augustus', '23 april', '15 juli'], 'correct' => '29 oktober'],
                        ['id' => 6, 'question' => 'Welk feest werd door Atatürk ingesteld als nationaal kinderfeest op 23 april?', 'options' => ['23 Nisan Ulusal Egemenlik ve Çocuk Bayramı', 'Gençlik ve Spor Bayramı', 'Zafer Bayramı', 'Demokrasi Bayramı'], 'correct' => '23 Nisan Ulusal Egemenlik ve Çocuk Bayramı'],
                        ['id' => 7, 'question' => 'Hoe heette het eerste parlement van de Republiek dat in 1920 in Ankara werd geopend?', 'options' => ['Türkiye Büyük Millet Meclisi (TBMM)', 'Osmanlı Meclisi', 'Ayan Meclisi', 'Milli Meclis'], 'correct' => 'Türkiye Büyük Millet Meclisi (TBMM)'],
                        ['id' => 8, 'question' => 'Welke politieke partij werd door Atatürk opgericht en was decennialang aan de macht?', 'options' => ['Democratische Partij', 'Cumhuriyet Halk Partisi (CHP)', 'Adalet Partisi', 'Vatan Partisi'], 'correct' => 'Cumhuriyet Halk Partisi (CHP)'],
                        ['id' => 9, 'question' => 'In welk jaar werd de hat (hoed) wet aangenomen ter vervanging van de fez?', 'options' => ['1923', '1925', '1928', '1934'], 'correct' => '1925'],
                        ['id' => 10, 'question' => 'Welk internationaal verdrag in 1936 gaf Turkije weer volledige controle over de Turkse zeestraten (Bosporus en Dardanellen)?', 'options' => ['Montreux Verdrag', 'Lausanne Verdrag', 'Sèvres Verdrag', 'Ankara Verdrag'], 'correct' => 'Montreux Verdrag'],
                        ['id' => 11, 'question' => 'Wie componeerde het Turkse volkslied (İstiklal Marşı)?', 'options' => ['Mehmet Akif Ersoy (tekst) & Osman Zeki Üngör (muziek)', 'Ahmet Hamdi', 'Ziya Gökalp', 'Itri'], 'correct' => 'Mehmet Akif Ersoy (tekst) & Osman Zeki Üngör (muziek)'],
                        ['id' => 12, 'question' => 'In welk jaar werd de Hagia Sophia voor het eerst omgebouwd tot moskee na de verovering van Istanbul?', 'options' => ['1453', '1520', '1600', '1923'], 'correct' => '1453'],
                        ['id' => 13, 'question' => 'Welke architect ontwierp wereldberoemde moskeeën zoals de Suleymaniye en Selimiye in de gouden Ottomaanse tijd?', 'options' => ['Mimar Sinan', 'Mimar Kemalettin', 'Sedefkar Mehmed Ağa', 'Balyan Ailesi'], 'correct' => 'Mimar Sinan'],
                        ['id' => 14, 'question' => 'Wat was de naam van het staatsruimteschip-programma of de historische spoorlijn die in de late Ottomaanse tijd werd aangelegd naar Medina?', 'options' => ['Hejaz Spoorlijn (Hicaz Demiryolu)', 'Anadolu Demiryolu', 'Bagdad Spoorlijn', 'Taurus Express'], 'correct' => 'Hejaz Spoorlijn (Hicaz Demiryolu)'],
                        ['id' => 15, 'question' => 'Welke sultan werd in 1876 afgezet nadat hij de eerste Ottomaanse grondwet (Kanun-ı Esasi) had afgekondigd?', 'options' => ['Abdulaziz', 'Murat V', 'Abdulhamid II', 'Mahmud II'], 'correct' => 'Murat V'],
                        ['id' => 16, 'question' => 'Hoe werd de periode van modernisering en grondwetsherstel in het late Ottomaanse Rijk (vanaf 1839) genoemd?', 'options' => ['Tanzimat (Hervormingen)', 'Istibdad', 'Meşrutiyet', 'Jong-Turkse Revolutie'], 'correct' => 'Tanzimat (Hervormingen)'],
                        ['id' => 17, 'question' => 'In welk jaar trad Turkije toe tot de NAVO?', 'options' => ['1945', '1949', '1952', '1955'], 'correct' => '1952'],
                        ['id' => 18, 'question' => 'Wie was de premier van Turkije die in 1950 aan de macht kwam via de eerste vrije democratische verkiezingen met geheime stemming?', 'options' => ['İsmet İnönü', 'Adnan Menderes', 'Celâl Bayar', 'Refik Saydam'], 'correct' => 'Adnan Menderes'],
                        ['id' => 19, 'question' => 'Welke beroemde vestingstad in Zuid-Oost Turkije staat bekend om haar indrukwekkende Romeinse muren en culturele erfgoed?', 'options' => ['Diyarbakır', 'Gaziantep', 'Şanlıurfa', 'Mardin'], 'correct' => 'Diyarbakır'],
                        ['id' => 20, 'question' => 'Wat is de naam van het archeologische werelderfgoedcomplex in Şanlıurfa dat wordt beschouwd als de oudste tempel ter wereld (ca. 9500 v.Chr.)?', 'options' => ['Çatalhöyük', 'Göbekli Tepe', 'Hattusa', 'Ephesus'], 'correct' => 'Göbekli Tepe']
                    ]
                ],
                3 => [
                    'title' => 'Variant 3: Cultuur & Monumenten',
                    'questions' => [
                        ['id' => 1, 'question' => 'Welke oude Griekse en Romeinse stad in West-Turkije stond bekend om de beroemde Celsus-bibliotheek?', 'options' => ['Troje', 'Efeze (Efes)', 'Pergamum', 'Miletus'], 'correct' => 'Efeze (Efes)'],
                        ['id' => 2, 'question' => 'In welke Turkse provincie vind je de unieke kalksteenterrassen van Pamukkale en de antieke stad Hierapolis?', 'options' => ['Denizli', 'Antalya', 'Muğla', 'İzmir'], 'correct' => 'Denizli'],
                        ['id' => 3, 'question' => 'Welk landschap in Midden-Anatolië staat bekend om zijn ondergrondse steden en unieke rotsformaties ("feeën Schoorstenen")?', 'options' => ['Cappadocië (Kapadokya)', 'Taurusgebergte', 'Zwarte Zee regio', 'Konya vlakte'], 'correct' => 'Cappadocië (Kapadokya)'],
                        ['id' => 4, 'question' => 'Welke antieke stad werd wereldberoemd door de legendarische Trojaanse Oorlog en het houten paard?', 'options' => ['Truva (Troje)', 'Sardis', 'Assos', 'Hierapolis'], 'correct' => 'Truva (Troje)'],
                        ['id' => 5, 'question' => 'Hoe heet de indrukwekkende tiende-eeuwse Armeense kerk op het eiland Akdamar in het Van-meer?', 'options' => ['Surb十字kerk (Akdamar Kilisesi)', 'Sumela Klooster', 'St. Pierre Kerk', 'Ayios Athanasios'], 'correct' => 'Surb十字kerk (Akdamar Kilisesi)'],
                        ['id' => 6, 'question' => 'Waar ligt het beroemde Grieks-Romeinse theater van Aspendos, dat bekend staat om zijn uitzonderlijke akoestiek?', 'options' => ['Antalya', 'İzmir', 'Mersin', 'Trabzon'], 'correct' => 'Antalya'],
                        ['id' => 7, 'question' => 'Welk historisch klooster in Trabzon is letterlijk gebouwd in een steile rotswand hoog boven de Zwarte Zee?', 'options' => ['Sumela Klooster (Sümela Manastırı)', 'Karmelietenkloster', 'Akdamar', 'St. Nicholas klooster'], 'correct' => 'Sumela Klooster (Sümela Manastırı)'],
                        ['id' => 8, 'question' => 'Welke historische brug in Mostar (Bosnië), gebouwd in de Ottomaanse tijd door Mimar Hayreddin, is wereldberoemd erfgoed?', 'options' => ['Stari Most (Oude Brug)', 'Fatih Brug', 'Kanuni Brug', 'Drina Brug'], 'correct' => 'Stari Most (Oude Brug)'],
                        ['id' => 9, 'question' => 'Welke Ottomaanse sultan verplaatste in 1365 de hoofdstad naar Edirne en bouwde daar de prachtige Selimiye-moskee?', 'options' => ['Murat I', 'Orhan Gazi', 'Bayezid I', 'Mehmed II'], 'correct' => 'Murat I'],
                        ['id' => 10, 'question' => 'Wat is de naam van het reusachtige reuzenrad of het paleis in Istanbul dat vroeger dienst deed als residentie van de Ottomaanse sultans (Topkapi)?', 'options' => ['Topkapı Sarayı', 'Dolmabahçe Sarayı', 'Çırağan Sarayı', 'Beylerbeyi Sarayı'], 'correct' => 'Topkapı Sarayı'],
                        ['id' => 11, 'question' => 'In welke stad ligt het indrukwekkende Mausoleum van Mevlana Celaleddin Rumi (de Whirling Dervishes)?', 'options' => ['Konya', 'Bursa', 'Ankara', 'Sivas'], 'correct' => 'Konya'],
                        ['id' => 12, 'question' => 'Welke regio in Noordoost-Turkije staat bekend om haar uitgestrekte theeplantages en groene bergen?', 'options' => ['Rize / Karadeniz', 'Aegeë', 'Marmara', 'Centraal Anatolië'], 'correct' => 'Rize / Karadeniz'],
                        ['id' => 13, 'question' => 'Hoe heet de beroemde zeestraat die de Zee van Marmara verbindt met de Egeïsche Zee?', 'options' => ['Dardanellen (Çanakkale Boğazı)', 'Bosporus', 'Gouden Hoorn', 'Izmit Golf'], 'correct' => 'Dardanellen (Çanakkale Boğazı)'],
                        ['id' => 14, 'question' => 'Welke Ottomaanse vizierfamilie (Köprülü) leverde in de 17e eeuw meerdere machtige grootviziers die het rijk hervormden?', 'options' => ['Köprülü familie', 'Sokollu familie', 'Çandarlı familie', 'İbrahim Pasha familie'], 'correct' => 'Köprülü familie'],
                        ['id' => 15, 'question' => 'Wat is de naam van de beroemde historische karavanserai (han) in Kayseri of Bursa die vroeger diende als handelscentrum langs de Zijderoute?', 'options' => ['Koza Han', 'Grand Bazaar', 'Misir Çarşısı', 'Rustem Pasha Han'], 'correct' => 'Koza Han'],
                        ['id' => 16, 'question' => 'Welke oude beschaving liet de indrukwekkende rotsmonumenten en reliëfs na in Hattusa, de hoofdstad nabij Çorum?', 'options' => ['Hettieten (Hititler)', 'Urartu', 'Frygiërs', 'Lydiërs'], 'correct' => 'Hettieten (Hititler)'],
                        ['id' => 17, 'question' => 'Wie was de legendarische 13e-eeuwse volksfilosoof en dichter bekend om zijn humoristische en wijze anekdotes met een ezel?', 'options' => ['Nasreddin Hoca', 'Yunus Emre', 'Karagöz', 'Mevlana'], 'correct' => 'Nasreddin Hoca'],
                        ['id' => 18, 'question' => 'In welke stad bevindt zich de beroemde "Cistern Basilica" (Yerebatan Sarnıcı), een ondergrondse waterreservoir uit de Byzantijnse tijd?', 'options' => ['Istanbul', 'Izmir', 'Antalya', 'Bursa'], 'correct' => 'Istanbul'],
                        ['id' => 19, 'question' => 'Welke berg in Oost-Anatolië wordt in legenden beschouwd als de rustplaats van de Ark van Noach?', 'options' => ['Berg Ararat (Ağrı Dağı)', 'Uludağ', 'Erciyes Dağı', 'Kaçkar Dağları'], 'correct' => 'Berg Ararat (Ağrı Dağı)'],
                        ['id' => 20, 'question' => 'In welk jaar werd de Republiek Turkije lid van de Raad van Europa (Avrupa Konseyi)?', 'options' => ['1949', '1952', '1959', '1963'], 'correct' => '1949']
                    ]
                ]
            ]
        ],
        'rekenquiz' => [
            'title' => 'Rekenquiz',
            'description' => 'Test je hoofdrekenen, logica en wiskundige vaardigheden.',
            'icon' => 'bi-calculator-fill',
            'color' => 'text-success',
            'variants' => [
                1 => [
                    'title' => 'Variant 1: Hoofdrekenen & Logica',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is 15 x 12?', 'options' => ['150', '180', '190', '210'], 'correct' => '180'],
                        ['id' => 2, 'question' => 'Wat is de wortel van 144?', 'options' => ['11', '12', '13', '14'], 'correct' => '12'],
                        ['id' => 3, 'question' => 'Als 3x + 5 = 20, wat is dan de waarde van x?', 'options' => ['3', '4', '5', '6'], 'correct' => '5'],
                        ['id' => 4, 'question' => 'Wat is 25% van 240?', 'options' => ['50', '60', '70', '80'], 'correct' => '60'],
                        ['id' => 5, 'question' => 'Wat is 7 tot de macht 2?', 'options' => ['14', '42', '49', '56'], 'correct' => '49'],
                        ['id' => 6, 'question' => 'Hoeveel is 1000 gedeeld door 8?', 'options' => ['100', '125', '150', '175'], 'correct' => '125'],
                        ['id' => 7, 'question' => 'Wat is het volgende getal in de reeks: 2, 4, 8, 16, ?', 'options' => ['24', '30', '32', '64'], 'correct' => '32'],
                        ['id' => 8, 'question' => 'Wat is 45 + 55 x 0 + 10?', 'options' => ['0', '55', '110', '100'], 'correct' => '55'],
                        ['id' => 9, 'question' => 'Wat is de omtrek van een vierkant met zijde 7 cm?', 'options' => ['14 cm', '21 cm', '28 cm', '49 cm'], 'correct' => '28 cm'],
                        ['id' => 10, 'question' => 'Hoeveel graden is de hoek van een rechthoekige driehoek samen?', 'options' => ['90', '180', '270', '360'], 'correct' => '180'],
                        ['id' => 11, 'question' => 'Wat is 9 x 9 - 9 / 9?', 'options' => ['0', '72', '80', '81'], 'correct' => '80'],
                        ['id' => 12, 'question' => 'Wat is 3/4 uitgedrukt in procenten?', 'options' => ['60%', '70%', '75%', '80%'], 'correct' => '75%'],
                        ['id' => 13, 'question' => 'Wat is het priemgetal in deze lijst?', 'options' => ['9', '15', '19', '21'], 'correct' => '19'],
                        ['id' => 14, 'question' => 'Wat is 12 x 12 - 44?', 'options' => ['100', '104', '110', '144'], 'correct' => '100'],
                        ['id' => 15, 'question' => 'Als 5 arbeiders 5 dagen over een klus doen, hoeveel dagen doen 10 arbeiders erover (aan zelfde tempo)?', 'options' => ['2.5 dagen', '5 dagen', '10 dagen', '1 dag'], 'correct' => '2.5 dagen'],
                        ['id' => 16, 'question' => 'Wat is 1000 - 347?', 'options' => ['653', '663', '743', '753'], 'correct' => '653'],
                        ['id' => 17, 'question' => 'Wat is de oppervlakte van een cirkel met straal 5 cm (pi = 3.14)?', 'options' => ['31.4 cm²', '78.5 cm²', '157 cm²', '25 cm²'], 'correct' => '78.5 cm²'],
                        ['id' => 18, 'question' => 'Wat is 17 x 3?', 'options' => ['41', '51', '54', '57'], 'correct' => '51'],
                        ['id' => 19, 'question' => 'Hoeveel is 2 tot de macht 5?', 'options' => ['16', '24', '32', '64'], 'correct' => '32'],
                        ['id' => 20, 'question' => 'Wat is de som van de eerste 10 natuurlijke getallen (1 t/m 10)?', 'options' => ['45', '50', '55', '60'], 'correct' => '55']
                    ]
                ],
                2 => [
                    'title' => 'Variant 2: Algebra & Breuken',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is 3/5 + 1/5?', 'options' => ['4/10', '4/5', '3/25', '2/5'], 'correct' => '4/5'],
                        ['id' => 2, 'question' => 'Los op: 8 + 4 x 2 - 3', 'options' => ['21', '13', '15', '10'], 'correct' => '13'],
                        ['id' => 3, 'question' => 'Wat is de inhoud (volume) van een kubus met ribbe van 4 cm?', 'options' => ['16 cm³', '32 cm³', '64 cm³', '128 cm³'], 'correct' => '64 cm³'],
                        ['id' => 4, 'question' => 'Als y = 2x + 3 en x = 4, wat is dan y?', 'options' => ['10', '11', '12', '14'], 'correct' => '11'],
                        ['id' => 5, 'question' => 'Wat is 180 gedeeld door 0.5?', 'options' => ['90', '180', '360', '720'], 'correct' => '360'],
                        ['id' => 6, 'question' => 'Hoeveel procent is 15 van de 60?', 'options' => ['15%', '20%', '25%', '30%'], 'correct' => '25%'],
                        ['id' => 7, 'question' => 'Wat is de som van de hoeken van een vierhoek?', 'options' => ['180 graden', '270 graden', '360 graden', '540 graden'], 'correct' => '360 graden'],
                        ['id' => 8, 'question' => 'Wat is 2/3 vermenigvuldigd met 3/4?', 'options' => ['5/7', '6/12 (= 1/2)', '1', '5/12'], 'correct' => '6/12 (= 1/2)'],
                        ['id' => 9, 'question' => 'Wat is de wortel van 225?', 'options' => ['13', '14', '15', '16'], 'correct' => '15'],
                        ['id' => 10, 'question' => 'Wat is 11 x 11?', 'options' => ['111', '121', '131', '141'], 'correct' => '121'],
                        ['id' => 11, 'question' => 'Als een jas na 20% korting € 80,- kost, wat was dan de originele prijs?', 'options' => ['€ 96,-', '€ 100,-', '€ 104,-', '€ 110,-'], 'correct' => '€ 100,-'],
                        ['id' => 12, 'question' => 'Wat is de kleinste gemene veelvoud (kgv) van 4 en 6?', 'options' => ['10', '12', '24', '2'], 'correct' => '12'],
                        ['id' => 13, 'question' => 'Wat is de grootste gemene deler (ggd) van 24 en 36?', 'options' => ['6', '8', '12', '18'], 'correct' => '12'],
                        ['id' => 14, 'question' => 'Wat is -5 + (-8) - (-3)?', 'options' => ['-10', '-16', '0', '-6'], 'correct' => '-10'],
                        ['id' => 15, 'question' => 'Wat is 0.75 als breuk geschreven in de eenvoudigste vorm?', 'options' => ['3/4', '7/10', '2/3', '4/5'], 'correct' => '3/4'],
                        ['id' => 16, 'question' => 'Hoeveel is 9 tot de macht 2 min 19?', 'options' => ['62', '72', '81', '100'], 'correct' => '62'],
                        ['id' => 17, 'question' => 'Wat is de inhoud van een cilinder met straal 2 en hoogte 10 (pi = 3)?', 'options' => ['120', '60', '40', '80'], 'correct' => '120'],
                        ['id' => 18, 'question' => 'Los op voor x: 5x - 10 = 25', 'options' => ['5', '7', '9', '15'], 'correct' => '7'],
                        ['id' => 19, 'question' => 'Wat is 13 x 13?', 'options' => ['156', '169', '179', '196'], 'correct' => '169'],
                        ['id' => 20, 'question' => 'Als een trein 120 km rijdt in 1 uur en 30 minuten, wat is dan de gemiddelde snelheid?', 'options' => ['75 km/u', '80 km/u', '90 km/u', '100 km/u'], 'correct' => '80 km/u']
                    ]
                ],
                3 => [
                    'title' => 'Variant 3: Puzzels & Cijfers',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is het volgende getal in de reeks: 1, 1, 2, 3, 5, 8, ?', 'options' => ['11', '12', '13', '15'], 'correct' => '13'],
                        ['id' => 2, 'question' => 'Als je 5 kippen in 5 dagen 5 eieren laten leggen, hoeveel eieren leggen 100 kippen dan in 100 dagen?', 'options' => ['100 eieren', '500 eieren', '2000 eieren', '10000 eieren'], 'correct' => '2000 eieren'],
                        ['id' => 3, 'question' => 'Wat is 50 van 200 als breuk?', 'options' => ['1/2', '1/4', '1/5', '1/3'], 'correct' => '1/4'],
                        ['id' => 4, 'question' => 'Hoeveel diagonalen heeft een achthoek (octogoon)?', 'options' => ['14', '20', '27', '35'], 'correct' => '20'],
                        ['id' => 5, 'question' => 'Wat is de helft van de helft van 100?', 'options' => ['25', '50', '12.5', '75'], 'correct' => '25'],
                        ['id' => 6, 'question' => 'Welk getal is deelbaar door zowel 3, 4 als 5?', 'options' => ['45', '60', '75', '90'], 'correct' => '60'],
                        ['id' => 7, 'question' => 'Wat is de som van de getallen van 1 tot en met 100 (Gauss methode)?', 'options' => ['4950', '5050', '5150', '5250'], 'correct' => '5050'],
                        ['id' => 8, 'question' => 'Hoeveel uren zitten er in één hele week?', 'options' => ['144 uur', '168 uur', '192 uur', '240 uur'], 'correct' => '168 uur'],
                        ['id' => 9, 'question' => 'Wat is 16 x 25?', 'options' => ['300', '350', '400', '450'], 'correct' => '400'],
                        ['id' => 10, 'question' => 'Wat is de derdemachtswortel van 64?', 'options' => ['2', '4', '8', '16'], 'correct' => '4'],
                        ['id' => 11, 'question' => 'Wat is 999 + 99 + 9?', 'options' => ['1097', '1107', '1117', '1077'], 'correct' => '1107'],
                        ['id' => 12, 'question' => 'Als je een taart in 8 gelijke punten snijdt en je eet er 3 op, hoeveel procent is er dan over?', 'options' => ['37.5%', '62.5%', '75%', '50%'], 'correct' => '62.5%'],
                        ['id' => 13, 'question' => 'Wat is 14 x 15?', 'options' => ['196', '210', '225', '240'], 'correct' => '210'],
                        ['id' => 14, 'question' => 'Welk getal ontbreekt in de reeks: 3, 9, 27, ?, 243', 'options' => ['54', '81', '108', '120'], 'correct' => '81'],
                        ['id' => 15, 'question' => 'Wat is de omtrek van een cirkel met straal 7 cm (pi = 22/7)?', 'options' => ['22 cm', '44 cm', '88 cm', '154 cm'], 'correct' => '44 cm'],
                        ['id' => 16, 'question' => 'Wat is de faculteit van 4 (notatie: 4!)?', 'options' => ['12', '16', '24', '32'], 'correct' => '24'],
                        ['id' => 17, 'question' => 'Als gistermiddag was wat morgen overmorgen is, welke dag is het vandaag als we het raadsel omdraaien? (Standaard logica: Wat is 50% van 200 + 50?)', 'options' => ['100', '150', '200', '250'], 'correct' => '150'],
                        ['id' => 18, 'question' => 'Wat is 2 tot de macht 10 (2^10)?', 'options' => ['512', '1024', '2048', '4096'], 'correct' => '1024'],
                        ['id' => 19, 'question' => 'Wat is 75 gedeeld door 0.25?', 'options' => ['150', '225', '300', '375'], 'correct' => '300'],
                        ['id' => 20, 'question' => 'Wat is het Romeinse cijfer voor 499?', 'options' => ['CDXCIX', 'ID', 'XDIX', 'LD'], 'correct' => 'CDXCIX']
                    ]
                ]
            ]
        ],
        'hoofdsteden' => [
            'title' => 'Hoofdsteden van Landen',
            'description' => 'Ken jij de wereldkaarten en hoofdsteden op je duimpje?',
            'icon' => 'bi-globe-americas',
            'color' => 'text-info',
            'variants' => [
                1 => [
                    'title' => 'Variant 1: Wereldwijde Hoofdsteden',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is de hoofdstad van Australië?', 'options' => ['Sydney', 'Melbourne', 'Canberra', 'Perth'], 'correct' => 'Canberra'],
                        ['id' => 2, 'question' => 'Wat is de hoofdstad van Canada?', 'options' => ['Toronto', 'Vancouver', 'Ottawa', 'Montreal'], 'correct' => 'Ottawa'],
                        ['id' => 3, 'question' => 'Wat is de hoofdstad van Japan?', 'options' => ['Kyoto', 'Osaka', 'Tokyo', 'Hiroshima'], 'correct' => 'Tokyo'],
                        ['id' => 4, 'question' => 'Wat is de hoofdstad van Brazilië?', 'options' => ['Rio de Janeiro', 'São Paulo', 'Brasília', 'Salvador'], 'correct' => 'Brasília'],
                        ['id' => 5, 'question' => 'Wat is de hoofdstad van Zuid-Afrika?', 'options' => ['Johannesburg', 'Kaapstad', 'Pretoria', 'Beide B en C'], 'correct' => 'Beide B en C'],
                        ['id' => 6, 'question' => 'Wat is de hoofdstad van Argentinië?', 'options' => ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza'], 'correct' => 'Buenos Aires'],
                        ['id' => 7, 'question' => 'Wat is de hoofdstad van India?', 'options' => ['Mumbai', 'New Delhi', 'Bangalore', 'Calcutta'], 'correct' => 'New Delhi'],
                        ['id' => 8, 'question' => 'Wat is de hoofdstad van Zuid-Korea?', 'options' => ['Busan', 'Incheon', 'Seoul', 'Daegu'], 'correct' => 'Seoul'],
                        ['id' => 9, 'question' => 'Wat is de hoofdstad van Nieuw-Zeeland?', 'options' => ['Auckland', 'Wellington', 'Christchurch', 'Hamilton'], 'correct' => 'Wellington'],
                        ['id' => 10, 'question' => 'Wat is de hoofdstad van Portugal?', 'options' => ['Porto', 'Coimbra', 'Lissabon', 'Faro'], 'correct' => 'Lissabon'],
                        ['id' => 11, 'question' => 'Wat is de hoofdstad van Zwitserland?', 'options' => ['Zürich', 'Genève', 'Bern', 'Lausanne'], 'correct' => 'Bern'],
                        ['id' => 12, 'question' => 'Wat is de hoofdstad van Marokko?', 'options' => ['Casablanca', 'Marrakesh', 'Rabat', 'Fez'], 'correct' => 'Rabat'],
                        ['id' => 13, 'question' => 'Wat is de hoofdstad van Thailand?', 'options' => ['Phuket', 'Bangkok', 'Chiang Mai', 'Pattaya'], 'correct' => 'Bangkok'],
                        ['id' => 14, 'question' => 'Wat is de hoofdstad van Mexico?', 'options' => ['Cancún', 'Guadalajara', 'Mexico-Stad', 'Monterrey'], 'correct' => 'Mexico-Stad'],
                        ['id' => 15, 'question' => 'Wat is de hoofdstad van Egypte?', 'options' => ['Alexandrië', 'Caïro', 'Luxor', 'Gizeh'], 'correct' => 'Caïro'],
                        ['id' => 16, 'question' => 'Wat is de hoofdstad van Noorwegen?', 'options' => ['Bergen', 'Trondheim', 'Oslo', 'Stavanger'], 'correct' => 'Oslo'],
                        ['id' => 17, 'question' => 'Wat is de hoofdstad van Vietnam?', 'options' => ['Ho Chi Minhstad', 'Hanoi', 'Da Nang', 'Hue'], 'correct' => 'Hanoi'],
                        ['id' => 18, 'question' => 'Wat is de hoofdstad van Kenia?', 'options' => ['Mombasa', 'Nairobi', 'Kisumu', 'Nakuru'], 'correct' => 'Nairobi'],
                        ['id' => 19, 'question' => 'Wat is de hoofdstad van Zweden?', 'options' => ['Göteborg', 'Malmö', 'Stockholm', 'Uppsala'], 'correct' => 'Stockholm'],
                        ['id' => 20, 'question' => 'Wat is de hoofdstad van Colombia?', 'options' => ['Medellín', 'Cali', 'Bogotá', 'Cartagena'], 'correct' => 'Bogotá']
                    ]
                ],
                2 => [
                    'title' => 'Variant 2: Europa & Azië',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is de hoofdstad van Frankrijk?', 'options' => ['Lyon', 'Marseille', 'Parijs', 'Nice'], 'correct' => 'Parijs'],
                        ['id' => 2, 'question' => 'Wat is de hoofdstad van Duitsland?', 'options' => ['München', 'Frankfurt', 'Berlijn', 'Hamburg'], 'correct' => 'Berlijn'],
                        ['id' => 3, 'question' => 'Wat is de hoofdstad van Spanje?', 'options' => ['Barcelona', 'Madrid', 'Valencia', 'Sevilla'], 'correct' => 'Madrid'],
                        ['id' => 4, 'question' => 'Wat is de hoofdstad van Italië?', 'options' => ['Milaan', 'Rome', 'Turijn', 'Florence'], 'correct' => 'Rome'],
                        ['id' => 5, 'question' => 'Wat is de hoofdstad van Turkije?', 'options' => ['Istanbul', 'Izmir', 'Ankara', 'Antalya'], 'correct' => 'Ankara'],
                        ['id' => 6, 'question' => 'Wat is de hoofdstad van Griekenland?', 'options' => ['Thessaloniki', 'Athene', 'Patras', 'Heraklion'], 'correct' => 'Athene'],
                        ['id' => 7, 'question' => 'Wat is de hoofdstad van Polen?', 'options' => ['Krakau', 'Warschau', 'Wroclaw', 'Gdansk'], 'correct' => 'Warschau'],
                        ['id' => 8, 'question' => 'Wat is de hoofdstad van Oostenrijk?', 'options' => ['Salzburg', 'Graz', 'Wenen', 'Innsbruck'], 'correct' => 'Wenen'],
                        ['id' => 9, 'question' => 'Wat is de hoofdstad van China?', 'options' => ['Shanghai', 'Peking (Beijing)', 'Hongkong', 'Shenzhen'], 'correct' => 'Peking (Beijing)'],
                        ['id' => 10, 'question' => 'Wat is de hoofdstad van Pakistan?', 'options' => ['Karachi', 'Lahore', 'Islamabad', 'Peshawar'], 'correct' => 'Islamabad'],
                        ['id' => 11, 'question' => 'Wat is de hoofdstad van Indonesië?', 'options' => ['Bali', 'Jakarta', 'Surabaya', 'Bandung'], 'correct' => 'Jakarta'],
                        ['id' => 12, 'question' => 'Wat is de hoofdstad van Maleisië?', 'options' => ['Penang', 'Kuala Lumpur', 'Johor Bahru', 'Malakka'], 'correct' => 'Kuala Lumpur'],
                        ['id' => 13, 'question' => 'Wat is de hoofdstad van de Filippijnen?', 'options' => ['Cebu', 'Davao', 'Manilla', 'Quezon City'], 'correct' => 'Manilla'],
                        ['id' => 14, 'question' => 'Wat is de hoofdstad van Saudi-Arabië?', 'options' => ['Jeddah', 'Mekka', 'Riyad', 'Medina'], 'correct' => 'Riyad'],
                        ['id' => 15, 'question' => 'Wat is de hoofdstad van de Verenigde Arabische Emiraten (VAE)?', 'options' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman'], 'correct' => 'Abu Dhabi'],
                        ['id' => 16, 'question' => 'Wat is de hoofdstad van Iran?', 'options' => ['Isfahan', 'Shiraz', 'Teheran', 'Tabriz'], 'correct' => 'Teheran'],
                        ['id' => 17, 'question' => 'Wat is de hoofdstad van Irak?', 'options' => ['Basra', 'Mosoel', 'Bagdad', 'Erbil'], 'correct' => 'Bagdad'],
                        ['id' => 18, 'question' => 'Wat is de hoofdstad van Rusland?', 'options' => ['Sint-Petersburg', 'Moskou', 'Novosibirsk', 'Kazan'], 'correct' => 'Moskou'],
                        ['id' => 19, 'question' => 'Wat is de hoofdstad van Oekraïne?', 'options' => ['Lviv', 'Odessa', 'Kiev', 'Charkov'], 'correct' => 'Kiev'],
                        ['id' => 20, 'question' => 'Wat is de hoofdstad van Ierland?', 'options' => ['Cork', 'Galway', 'Dublin', 'Belfast'], 'correct' => 'Dublin']
                    ]
                ],
                3 => [
                    'title' => 'Variant 3: Afrika & Amerika',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat is de hoofdstad van de Verenigde Staten?', 'options' => ['New York', 'Los Angeles', 'Washington D.C.', 'Chicago'], 'correct' => 'Washington D.C.'],
                        ['id' => 2, 'question' => 'Wat is de hoofdstad van Chili?', 'options' => ['Valparaíso', 'Santiago', 'Concepción', 'Antofagasta'], 'correct' => 'Santiago'],
                        ['id' => 3, 'question' => 'Wat is de hoofdstad van Peru?', 'options' => ['Cusco', 'Lima', 'Arequipa', 'Trujillo'], 'correct' => 'Lima'],
                        ['id' => 4, 'question' => 'Wat is de hoofdstad van Venezuela?', 'options' => ['Maracaibo', 'Valencia', 'Caracas', 'Barquisimeto'], 'correct' => 'Caracas'],
                        ['id' => 5, 'question' => 'Wat is de hoofdstad van Uruguay?', 'options' => ['Montevideo', 'Punta del Este', 'Salto', 'Colonia'], 'correct' => 'Montevideo'],
                        ['id' => 6, 'question' => 'Wat is de hoofdstad van Nigeria?', 'options' => ['Lagos', 'Abuja', 'Kano', 'Ibadan'], 'correct' => 'Abuja'],
                        ['id' => 7, 'question' => 'Wat is de hoofdstad van Ghana?', 'options' => ['Kumasi', 'Accra', 'Tamale', 'Takoradi'], 'correct' => 'Accra'],
                        ['id' => 8, 'question' => 'Wat is de hoofdstad van Ethiopië?', 'options' => ['Gondar', 'Addis Abeba', 'Dire Dawa', 'Bahir Dar'], 'correct' => 'Addis Abeba'],
                        ['id' => 9, 'question' => 'Wat is de hoofdstad van Tanzania?', 'options' => ['Dar es Salaam', 'Dodoma', 'Arusha', 'Mwanza'], 'correct' => 'Dodoma'],
                        ['id' => 10, 'question' => 'Wat is de hoofdstad van Senegal?', 'options' => ['Thiès', 'Saint-Louis', 'Dakar', 'Ziguinchor'], 'correct' => 'Dakar'],
                        ['id' => 11, 'question' => 'Wat is de hoofdstad van Cuba?', 'options' => ['Santiago de Cuba', 'Camagüey', 'Havana', 'Varadero'], 'correct' => 'Havana'],
                        ['id' => 12, 'question' => 'Wat is de hoofdstad van Jamaica?', 'options' => ['Montego Bay', 'Kingston', 'Ocho Rios', 'Port Antonio'], 'correct' => 'Kingston'],
                        ['id' => 13, 'question' => 'Wat is de hoofdstad van Costa Rica?', 'options' => ['Limón', 'Alajuela', 'San José', 'Heredia'], 'correct' => 'San José'],
                        ['id' => 14, 'question' => 'Wat is de hoofdstad van Panama?', 'options' => ['Colón', 'Panama-Stad', 'David', 'Santiago'], 'correct' => 'Panama-Stad'],
                        ['id' => 15, 'question' => 'Wat is de hoofdstad van Algerije?', 'options' => ['Oran', 'Constantine', 'Algiers', 'Annaba'], 'correct' => 'Algiers'],
                        ['id' => 16, 'question' => 'Wat is de hoofdstad van Tunesië?', 'options' => ['Sfax', 'Sousse', 'Tunis', 'Bizerte'], 'correct' => 'Tunis'],
                        ['id' => 17, 'question' => 'Wat is de hoofdstad van Kameroen?', 'options' => ['Douala', 'Yaoundé', 'Bamenda', 'Garoua'], 'correct' => 'Yaoundé'],
                        ['id' => 18, 'question' => 'Wat is de hoofdstad van Angola?', 'options' => ['Lubango', 'Cabinda', 'Luanda', 'Huambo'], 'correct' => 'Luanda'],
                        ['id' => 19, 'question' => 'Wat is de hoofdstad van Ecuador?', 'options' => ['Guayaquil', 'Quito', 'Cuenca', 'Manta'], 'correct' => 'Quito'],
                        ['id' => 20, 'question' => 'Wat is de hoofdstad van Bolivia?', 'options' => ['La Paz (regering) & Sucre (grondwet)', 'Santa Cruz', 'Cochabamba', 'Oruro'], 'correct' => 'La Paz (regering) & Sucre (grondwet)']
                    ]
                ]
            ]
        ],
        'taalquiz' => [
            'title' => 'Taalquiz',
            'description' => 'Spelling, grammatica, spreekwoorden en taalkennis.',
            'icon' => 'bi-journal-richtext',
            'color' => 'text-primary',
            'variants' => [
                1 => [
                    'title' => 'Variant 1: Grammatica & Spelling',
                    'questions' => [
                        ['id' => 1, 'question' => 'Hoe schrijf je dit correct in het Nederlands?', 'options' => ['Cacao', 'Kakao', 'Kakaow', 'Kacou'], 'correct' => 'Cacao'],
                        ['id' => 2, 'question' => 'Wat is de verleden tijd van "verwoorden"?', 'options' => ['verwoorde', 'verwoordde', 'verwoordeerde', 'verwoord'], 'correct' => 'verwoordde'],
                        ['id' => 3, 'question' => 'Wat betekent het spreekwoord "De kogel is door de kerk"?', 'options' => ['Het gevaar is geweken', 'De beslissing is genomen', 'Er is ruzie ontstaan', 'De kerk is beschadigd'], 'correct' => 'De beslissing is genomen'],
                        ['id' => 4, 'question' => 'Welk woord is een synoniem voor "abrupt"?', 'options' => ['Geleidelijk', 'Plotseling', 'Langzaam', 'Voorzichtig'], 'correct' => 'Plotseling'],
                        ['id' => 5, 'question' => 'Hoe schrijf je de overtreffende trap van "goed"?', 'options' => ['Beter', 'Best', 'Goedst', 'Meest goed'], 'correct' => 'Best'],
                        ['id' => 6, 'question' => 'Wat is de correcte spelling?', 'options' => ['Pannenkoek', 'Pannekoek', 'Pannenkook', 'Pannekook'], 'correct' => 'Pannenkoek'],
                        ['id' => 7, 'question' => 'Wat betekent het woord "ambivalent"?', 'options' => ['Tweeduidig / dub gevoelend', 'Zeer ambitieus', 'Heel vriendelijk', 'Volledig neutraal'], 'correct' => 'Tweeduidig / dub gevoelend'],
                        ['id' => 8, 'question' => 'Welk van deze woorden is een palindroom (leest achterstevoren hetzelfde)?', 'options' => ['Leven', 'Lepel', 'Stoel', 'Tafel'], 'correct' => 'Lepel'],
                        ['id' => 9, 'question' => 'Wat is de correcte meervoudsvorm van "cactussen" of...', 'options' => ['Cacti', 'Cactussen', 'Beide zijn correct', 'Cactussev'], 'correct' => 'Beide zijn correct'],
                        ['id' => 10, 'question' => 'Wat betekent de uitdrukking "Botervierkant"?', 'options' => ['Heel vet', 'Helemaal juist / absoluut', 'Vierkant gevormd', 'Ouderwets'], 'correct' => 'Helemaal juist / absoluut'],
                        ['id' => 11, 'question' => 'Hoe schrijf je het voltooid deelwoord van "gebeuren"?', 'options' => ['Gegéven', 'Gebeurd', 'Geboord', 'Gebeurt'], 'correct' => 'Gebeurd'],
                        ['id' => 12, 'question' => 'Wat is een antoniem (tegenovergestelde) van "optimistisch"?', 'options' => ['Realistisch', 'Pessimistisch', 'Enthousiast', 'Sceptisch'], 'correct' => 'Pessimistisch'],
                        ['id' => 13, 'question' => 'Welke spelling is juist?', 'options' => ['Secretaresse', 'Secritaresse', 'Secretares', 'Secreetarese'], 'correct' => 'Secretaresse'],
                        ['id' => 14, 'question' => 'Wat betekent de uitdrukking "Een kat in de zak kopen"?', 'options' => ['Een huisdier adopteren', 'Bedrogen uitkomen bij een koop', 'Een geweldige deal sluiten', 'Een lege doos krijgen'], 'correct' => 'Bedrogen uitkomen bij een koop'],
                        ['id' => 15, 'question' => 'Wat is de juiste vervoeging: "Hij ... gisteren naar de winkel."', 'options' => ['loop', 'liep', 'gelopen', 'loopte'], 'correct' => 'liep'],
                        ['id' => 16, 'question' => 'Wat is de correcte spelling van het leenwoord?', 'options' => ['Bureau', 'Buro', 'Bureaux', 'Burau'], 'correct' => 'Bureau'],
                        ['id' => 17, 'question' => 'Wat betekent "pro deo"?', 'options' => ['Voor God', 'Gratis / voor niets', 'Duur betaald', 'Religieus'], 'correct' => 'Gratis / voor niets'],
                        ['id' => 18, 'question' => 'Welk woord is correct gespeld?', 'options' => ['Cadeau', 'Kado', 'Cado', 'Kadeau'], 'correct' => 'Cadeau'],
                        ['id' => 19, 'question' => 'Wat betekent het gezegde "De aap komt uit de mouw"?', 'options' => ['Er is een dier ontsnapt', 'De ware aard of intentie wordt duidelijk', 'Je hebt geluk', 'Het feest begint'], 'correct' => 'De ware aard of intentie wordt duidelijk'],
                        ['id' => 20, 'question' => 'Hoeveel klinkers telt het woord "aardappel"?', 'options' => ['3', '4', '5', '6'], 'correct' => '4']
                    ]
                ],
                2 => [
                    'title' => 'Variant 2: Spreekwoorden & Uitdrukkingen',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat betekent het spreekwoord "Wie een kuil graaft voor een ander, valt er zelf in"?', 'options' => ['Je moet geen gaten maken', 'Wie slechte plannen maakt voor een ander, wordt daar zelf dupe van', 'Tuinieren is gevaarlijk', 'Je krijgt altijd hulp'], 'correct' => 'Wie slechte plannen maakt voor een ander, wordt daar zelf dupe van'],
                        ['id' => 2, 'question' => 'Wat betekent "De aarde schudt onder zijn voeten"?', 'options' => ['Hij heeft schoenen met veren', 'Hij bevindt zich in een zeer onstabiele of bedreigende situatie', 'Er is een aardbeving', 'Hij danst erg hard'], 'correct' => 'Hij bevindt zich in een zeer onstabiele of bedreigende situatie'],
                        ['id' => 3, 'question' => 'Hoe luidt het vervolg van: "Beter een vogel in de hand..."?', 'options' => ['...dan tien in de boom', '...dan de lucht', '...dan tien in de lucht', '...dan een kat in de zak'], 'correct' => '...dan tien in de lucht'],
                        ['id' => 4, 'question' => 'Wat betekent "Spijkers op laag water zoeken"?', 'options' => ['Vissen naar spijkers', 'Muggenziften / zeuren over onbelangrijke details', 'Duiken in een meer', 'Gereedschap opruimen'], 'correct' => 'Muggenziften / zeuren over onbelangrijke details'],
                        ['id' => 5, 'question' => 'Wat betekent "Hoge bomen vangen veel wind"?', 'options' => ['In bossen waait het altijd hard', 'Mensen met een hoge of invloedrijke positie krijgen ook de meeste kritiek te verduren', 'Lange mensen hebben koude hoofden', 'Je moet niet hoog bouwen'], 'correct' => 'Mensen met een hoge of invloedrijke positie krijgen ook de meeste kritiek te verduren'],
                        ['id' => 6, 'question' => 'Welk woord is een correct antoniem voor "Vrijgevig"?', 'options' => ['Gierig', 'Rijk', 'Arm', 'Vriendelijk'], 'correct' => 'Gierig'],
                        ['id' => 7, 'question' => 'Wat is de correcte verleden tijd van "glimlachen"?', 'options' => ['glimlachte', 'glimlachde', 'geglimlacht', 'glimlachte / geglimlacht'], 'correct' => 'glimlachte'],
                        ['id' => 8, 'question' => 'Wat betekent de uitdrukking "Botten breken"?', 'options' => ['Valpartij', 'Heel hard werken / zware inspanning', 'Osteoporose', 'Spierpijn'], 'correct' => 'Heel hard werken / zware inspanning'],
                        ['id' => 9, 'question' => 'Hoe schrijf je de meervoud van "cadeau"?', 'options' => ['Cadeaux', 'Cadeaus', 'Beide zijn correct', 'Cadau\'s'], 'correct' => 'Beide zijn correct'],
                        ['id' => 10, 'question' => 'Wat betekent "De spuigaten uitlopen"?', 'options' => ['Te erg worden / de spuigaten passeren', 'Lekkende boten', 'Schoonmaken', 'Te veel water drinken'], 'correct' => 'Te erg worden / de spuigaten passeren'],
                        ['id' => 11, 'question' => 'Wat is de betekenis van het leenwoord "Fiasco"?', 'options' => ['Een groot succes', 'Een totale mislukking', 'Een feest', 'Een muziekinstrument'], 'correct' => 'Een totale mislukking'],
                        ['id' => 12, 'question' => 'Welke spelling van het voltooid deelwoord is correct?', 'options' => ['Hij heeft gefietst', 'Hij heeft gefiets', 'Hij is gefietst', 'Zowel A als C is afhankelijk van context'], 'correct' => 'Zowel A als C is afhankelijk van context'],
                        ['id' => 13, 'question' => 'Wat betekent "Met de kippen op stok gaan"?', 'options' => ['Vroeg naar bed gaan', 'Kippen voeren', 'Biologisch boeren', 'Op vakantie gaan'], 'correct' => 'Vroeg naar bed gaan'],
                        ['id' => 14, 'question' => 'Wat is de correcte vervoeging van "antwoorden" in de verleden tijd (ik...)?', 'options' => ['antwoorde', 'antwoordde', 'antwoortde', 'antwoordeerde'], 'correct' => 'antwoordde'],
                        ['id' => 15, 'question' => 'Wat betekent het gezegde "Stille waters hebben diepe gronden"?', 'options' => ['Zwembaden zijn diep', 'Rustige of zwijgzame mensen kunnen heel diepgaande of onverwachte gedachtes hebben', 'Het water stroomt niet', 'Stilte is goud waard'], 'correct' => 'Rustige of zwijgzame mensen kunnen heel diepgaande of onverwachte gedachtes hebben'],
                        ['id' => 16, 'question' => 'Welk woord is correct gespeld?', 'options' => ['Vrijwilliger', 'Vrijwilligener', 'Vrijwilligerer', 'Vrijwillger'], 'correct' => 'Vrijwilliger'],
                        ['id' => 17, 'question' => 'Wat betekent het Latijnse gezegde "Carpe Diem"?', 'options' => ['Pluk de dag', 'Memento mori', 'Tot morgen', 'Leef lang'], 'correct' => 'Pluk de dag'],
                        ['id' => 18, 'question' => 'Hoe schrijf je de verkleinwoord van "kasteel"?', 'options' => ['Kasteeltje', 'Kasteletje', 'Kastëltje', 'Kasteeltien'], 'correct' => 'Kasteeltje'],
                        ['id' => 19, 'question' => 'Wat betekent de uitdrukking "Olie op het vuur gooien"?', 'options' => ['Brand blussen', 'Een conflict verergeren', 'Koken', 'Energie besparen'], 'correct' => 'Een conflict verergeren'],
                        ['id' => 20, 'question' => 'Wat is de correcte meervoudsvorm van "aardappel"?', 'options' => ['Aardappels', 'Aardappelen', 'Beide zijn correct', 'Aardappelts'], 'correct' => 'Beide zijn correct']
                    ]
                ],
                3 => [
                    'title' => 'Variant 3: Grammatica & Vreemde Woorden',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wat betekent het woord "Pragmatisch"?', 'options' => ['Theoretisch', 'Handelend naar de praktijk / doelgericht', 'Eigenwijs', 'Onwetend'], 'correct' => 'Handelend naar de praktijk / doelgericht'],
                        ['id' => 2, 'question' => 'Hoe schrijf je de genitivus/bezitsvorm van "Anna"?', 'options' => ['Annas', 'Anna\'s', 'Annas\'', 'Annes'], 'correct' => 'Anna\'s'],
                        ['id' => 3, 'question' => 'Wat is de betekenis van het woord "Eufemisme"?', 'options' => ['Een grof scheldwoord', 'Een verzachtende of verbloemende omschrijving', 'Een medische term', 'Een spelfout'], 'correct' => 'Een verzachtende of verbloemende omschrijving'],
                        ['id' => 4, 'question' => 'Welk woord is correct gespeld volgens de officiële spelling?', 'options' => ['Akkoord', 'Accoord', 'Acoord', 'Accoordt'], 'correct' => 'Akkoord'],
                        ['id' => 5, 'question' => 'Wat betekent het woord "Apathisch"?', 'options' => ['Heel sympathiek', 'Vrij en blij', 'Zonder gevoel of interesse / onverschillig', 'Angstig'], 'correct' => 'Zonder gevoel of interesse / onverschillig'],
                        ['id' => 6, 'question' => 'Wat is de correcte vervoeging in de verleden tijd van "verlegen"?', 'options' => ['Verleegde', 'Verlegde', 'Verloeg', 'Verlegden'], 'correct' => 'Verlegde'],
                        ['id' => 7, 'question' => 'Wat betekent de term "Status quo"?', 'options' => ['De huidige toestand / stand van zaken', 'Een hoge status', 'Een juridisch contract', 'Verandering'], 'correct' => 'De huidige toestand / stand van zaken'],
                        ['id' => 8, 'question' => 'Welk van deze woorden gebruikt een apostrof voor het meervoud (klinkerbotsing)?', 'options' => ['Auto\'s', 'Bomen', 'Katten', 'Honden'], 'correct' => 'Auto\'s'],
                        ['id' => 9, 'question' => 'Wat betekent "Pro bono"?', 'options' => ['Voor de winst', 'Kosteloos / voor het algemeen belang', 'Botten brekend', 'Professioneel'], 'correct' => 'Kosteloos / voor het algemeen belang'],
                        ['id' => 10, 'question' => 'Wat is de juiste spelling van het woord dat "noodzakelijk" betekent?', 'options' => ['Noodzakelijk', 'Noodzaaklijk', 'Nootsakelijk', 'Noodzakeliik'], 'correct' => 'Noodzakelijk'],
                        ['id' => 11, 'question' => 'Wat is een homoniem?', 'options' => ['Woorden die hetzelfde klinken of geschreven worden maar een andere betekenis hebben', 'Woorden met een tegenovergestelde betekenis', 'Grammaticaal foutieve woorden', 'Spelfouten'], 'correct' => 'Woorden die hetzelfde klinken of geschreven worden maar een andere betekenis hebben'],
                        ['id' => 12, 'question' => 'Wat betekent het woord "Substantieel"?', 'options' => ['Klein en nietig', 'Wezenlijk / aanzienlijk', 'Ondergronds', 'Subtiel'], 'correct' => 'Wezenlijk / aanzienlijk'],
                        ['id' => 13, 'question' => 'Welke spelling is correct voor het beroep?', 'options' => ['Chirurg', 'Sherug', 'Kirurg', 'Cirurgiër'], 'correct' => 'Chirurg'],
                        ['id' => 14, 'question' => 'Wat betekent "In casu"?', 'options' => ['In dit geval', 'In de kassa', 'In elk geval', 'Zonder twijfel'], 'correct' => 'In dit geval'],
                        ['id' => 15, 'question' => 'Hoe schrijf je het meervoud van "collega"?', 'options' => ['Collega\'s', 'Collegas', 'Collegas\'', 'Collegaen'], 'correct' => 'Collega\'s'],
                        ['id' => 16, 'question' => 'Wat betekent het woord "Triviaal"?', 'options' => ['Heel belangrijk', 'Alledaags / onbelangrijk', 'Geheimzinnig', 'Gecompliceerd'], 'correct' => 'Alledaags / onbelangrijk'],
                        ['id' => 17, 'question' => 'Welk woord is een synoniem voor "Frequent"?', 'options' => ['Zeldzaam', 'Vaak voorkomend', 'Luidruchtig', 'Kortstondig'], 'correct' => 'Vaak voorkomend'],
                        ['id' => 18, 'question' => 'Wat betekent "Tabula rasa"?', 'options' => ['Een schone lei / onbeschreven blad', 'Een volle tafel', 'Een wetboek', 'Een strafblad'], 'correct' => 'Een schone lei / onbeschreven blad'],
                        ['id' => 19, 'question' => 'Hoe schrijf je het voltooid deelwoord van "reserveren"?', 'options' => ['Gereserveerd', 'Gereserveert', 'Reseerveerd', 'Reserverde'], 'correct' => 'Gereserveerd'],
                        ['id' => 20, 'question' => 'Wat betekent het woord "Dilemma"?', 'options' => ['Een makkelijke keuze', 'Een keuze tussen twee ongunstige of lastige alternatieven', 'Een groot succes', 'Een medische ingreep'], 'correct' => 'Een keuze tussen twee ongunstige of lastige alternatieven']
                    ]
                ]
            ]
        ],
        'geschiedenis_algemeen' => [
            'title' => 'Algemene Geschiedenis',
            'description' => 'Ontdek wereldschokkende gebeurtenissen, rijken en historische figuren.',
            'icon' => 'bi-hourglass-split',
            'color' => 'text-purple',
            'variants' => [
                1 => [
                    'title' => 'Variant 1: Wereldgeschiedenis & Oudheid',
                    'questions' => [
                        ['id' => 1, 'question' => 'Welke oude beschaving bouwde de piramides van Gizeh?', 'options' => ['Romeinen', 'Grieken', 'Oude Egyptenaren', 'Perzen'], 'correct' => 'Oude Egyptenaren'],
                        ['id' => 2, 'question' => 'Wie was de beroemde Carthaagse generaal die met olifanten de Alpen overstak?', 'options' => ['Julius Caesar', 'Hannibal', 'Alexander de Grote', 'Spartacus'], 'correct' => 'Hannibal'],
                        ['id' => 3, 'question' => 'In welk jaar viel het Romeinse Rijk in het Westen definitief?', 'options' => ['476 n.Chr.', '1453 n.Chr.', '330 n.Chr.', '100 v.Chr.'], 'correct' => '476 n.Chr.'],
                        ['id' => 4, 'question' => 'Wie schreef het invloedrijke historische werk over de Peloponnesische Oorlog?', 'options' => ['Homerus', 'Herodotus', 'Thucydides', 'Plato'], 'correct' => 'Thucydides'],
                        ['id' => 5, 'question' => 'Welke keizer liet de Chinese Muur in grote mate verbinden en versterken?', 'options' => ['Qin Shi Huangdi', 'Kublai Khan', 'Sun Yat-sen', 'Mao Zedong'], 'correct' => 'Qin Shi Huangdi'],
                        ['id' => 6, 'question' => 'Hoe heette het economische en politieke systeem in Europa tijdens de Middeleeuwen met vazallen en leenheren?', 'options' => ['Kapitalisme', 'Feodalisme (Leenstelsel)', 'Mercantilisme', 'Socialisme'], 'correct' => 'Feodalisme (Leenstelsel)'],
                        ['id' => 7, 'question' => 'Welke pandemie decimeerde Europa in het midden van de 14e eeuw?', 'options' => ['De Spaanse Griek', 'De Zwarte Dood (Pest)', 'Cholera', 'Pokken'], 'correct' => 'De Zwarte Dood (Pest)'],
                        ['id' => 8, 'question' => 'In welk jaar bereikte Christoffel Columbus het Amerikaanse continent?', 'options' => ['1488', '1492', '1500', '1512'], 'correct' => '1492'],
                        ['id' => 9, 'question' => 'Welke beweging in de 15e en 16e eeuw betekende de "wedergeboorte" van kunst en wetenschap in Europa?', 'options' => ['De Verlichting', 'De Reformatie', 'De Renaissance', 'De Industriële Revolutie'], 'correct' => 'De Renaissance'],
                        ['id' => 10, 'question' => 'Wie startte de Protestantse Reformatie in 1517 door zijn stellingen te publiceren?', 'options' => ['Johannes Calvijn', 'Maarten Luther', 'Hendrik VIII', 'Paus Leo X'], 'correct' => 'Maarten Luther'],
                        ['id' => 11, 'question' => 'Welke Franse koning stond bekend als de "Zonnekoning"?', 'options' => ['Lodewijk XIV', 'Lodewijk XVI', 'Napoleon Bonaparte', 'Karel de Grote'], 'correct' => 'Lodewijk XIV'],
                        ['id' => 12, 'question' => 'In welk jaar vond de bestorming van de Bastille plaats tijdens de Franse Revolutie?', 'options' => ['1776', '1789', '1815', '1830'], 'correct' => '1789'],
                        ['id' => 13, 'question' => 'Op welk eiland werd Napoleon Bonaparte definitief verslagen en verbannen tot zijn dood?', 'options' => ['Elba', 'Corsica', 'Sint-Helena', 'Sicilië'], 'correct' => 'Sint-Helena'],
                        ['id' => 14, 'question' => 'Welke eeuw wordt beschouwd als de Eeuw van de Verlichting?', 'options' => ['16e eeuw', '17e eeuw', '18e eeuw', '19e eeuw'], 'correct' => '18e eeuw'],
                        ['id' => 15, 'question' => 'Welke uitvinding luidde in de 18e eeuw de Industriële Revolutie in?', 'options' => ['De stoommachine', 'De auto', 'De gloeilamp', 'De drukpers'], 'correct' => 'De stoommachine'],
                        ['id' => 16, 'question' => 'Welk verdrag maakte een einde aan de Dertigjarige Oorlog in 1648?', 'options' => ['Verdrag van Utrecht', 'Vrede van Westfalen', 'Verdrag van Versailles', 'Vrede van Wenen'], 'correct' => 'Vrede van Westfalen'],
                        ['id' => 17, 'question' => 'Wie was de leider van de Oktoberrevolutie in Rusland in 1917?', 'options' => ['Tsaar Nicolaas II', 'Joseph Stalin', 'Vladimir Lenin', 'Leon Trotski'], 'correct' => 'Vladimir Lenin'],
                        ['id' => 18, 'question' => 'In welk jaar brak de Eerste Wereldoorlog uit?', 'options' => ['1912', '1914', '1918', '1939'], 'correct' => '1914'],
                        ['id' => 19, 'question' => 'Welke gebeurtenis leidde in 1941 tot de deelname van de Verenigde Staten aan de Tweede Wereldoorlog?', 'options' => ['D-Day', 'De aanval op Pearl Harbor', 'Het bombardement op Londen', 'De val van Berlijn'], 'correct' => 'De aanval op Pearl Harbor'],
                        ['id' => 20, 'question' => 'In welk jaar viel de Berlijnse Muur?', 'options' => ['1987', '1989', '1991', '1993'], 'correct' => '1989']
                    ]
                ],
                2 => [
                    'title' => 'Variant 2: Revoluties & Moderne Tijd',
                    'questions' => [
                        ['id' => 1, 'question' => 'Wie was de Amerikaanse president tijdens de Amerikaanse Burgeroorlog en schafte de slavernij af?', 'options' => ['George Washington', 'Thomas Jefferson', 'Abraham Lincoln', 'Theodore Roosevelt'], 'correct' => 'Abraham Lincoln'],
                        ['id' => 2, 'question' => 'In welk jaar vond de Amerikaanse Onafhankelijkheidsoorlog zijn start met de Verklaring van Onafhankelijkheid?', 'options' => ['1776', '1789', '1812', '1865'], 'correct' => '1776'],
                        ['id' => 3, 'question' => 'Welke ontdekkingsreiziger bereikte als eerste de Zuidpool in 1911?', 'options' => ['Robert Falcon Scott', 'Roald Amundsen', 'Ernest Shackleton', 'James Cook'], 'correct' => 'Roald Amundsen'],
                        ['id' => 4, 'question' => 'Wat was de directe aanleiding voor het uitbreken van de Eerste Wereldoorlog in Sarajevo?', 'options' => ['De invasie van Polen', 'De moord op Aartshertog Franz Ferdinand', 'Het zinken van de Lusitania', 'De Russische Revolutie'], 'correct' => 'De moord op Aartshertog Franz Ferdinand'],
                        ['id' => 5, 'question' => 'In welk jaar eindigde de Tweede Wereldoorlog?', 'options' => ['1944', '1945', '1946', '1950'], 'correct' => '1945'],
                        ['id' => 6, 'question' => 'Wie was de leider van de Sovjet-Unie tijdens het grootste deel van de Tweede Wereldoorlog?', 'options' => ['Vladimir Lenin', 'Joseph Stalin', 'Nikita Chroesjtsjov', 'Leonid Brezjnev'], 'correct' => 'Joseph Stalin'],
                        ['id' => 7, 'question' => 'Hoe heette het project van de VS dat tijdens de Tweede Wereldoorlog de eerste atoombom ontwikkelde?', 'options' => ['Apollo Project', 'Manhattan Project', 'Trinity Project', 'Enigma Project'], 'correct' => 'Manhattan Project'],
                        ['id' => 8, 'question' => 'Welke muur deelde de Duitse stad Berlijn van 1961 tot 1989?', 'options' => ['De IJzeren Gordijn', 'De Berlijnse Muur', 'De Hadrianusmuur', 'De Limes'], 'correct' => 'De Berlijnse Muur'],
                        ['id' => 9, 'question' => 'In welk jaar zette de mens (Neil Armstrong) voor het eerst voet op de maan?', 'options' => ['1967', '1969', '1971', '1973'], 'correct' => '1969'],
                        ['id' => 10, 'question' => 'Welke politieke crisis in 1962 bracht de VS en de Sovjet-Unie heel dicht bij een kernoorlog?', 'options' => ['De Suezcrisis', 'De Cubaanse Raketcrisis', 'De Berlijn-blokkade', 'De Vietnamoorlog'], 'correct' => 'De Cubaanse Raketcrisis'],
                        ['id' => 11, 'question' => 'Wie was de eerste mens in de ruimte (1961)?', 'options' => ['Neil Armstrong', 'Buzz Aldrin', 'Joeri Gagarin', 'John Glenn'], 'correct' => 'Joeri Gagarin'],
                        ['id' => 12, 'question' => 'Welk rijk stond bekend als "het rijk waar de zon nooit onderging" op zijn hoogtepunt in de 19e en 20e eeuw?', 'options' => ['Het Romeinse Rijk', 'Het Britse Rijk', 'Het Spaanse Rijk', 'Het Ottomaanse Rijk'], 'correct' => 'Het Britse Rijk'],
                        ['id' => 13, 'question' => 'In welk jaar werd de Verenigde Naties (VN) opgericht na de Tweede Wereldoorlog?', 'options' => ['1943', '1945', '1948', '1950'], 'correct' => '1945'],
                        ['id' => 14, 'question' => 'Welke veldslag in 1815 betekende de definitieve ondergang van Napoleon?', 'options' => ['Slag bij Waterloo', 'Slag bij Austerlitz', 'Slag bij Leipzig', 'Slag bij Trafalgar'], 'correct' => 'Slag bij Waterloo'],
                        ['id' => 15, 'question' => 'Wie was de koningin van Engeland tijdens het Victoriaanse tijdperk (1837–1901)?', 'options' => ['Koningin Elizabeth I', 'Koningin Victoria', 'Koningin Anne', 'Koningin Mary'], 'correct' => 'Koningin Victoria'],
                        ['id' => 16, 'question' => 'Welke revolutie in 1959 bracht Fidel Castro aan de macht in een naburig land van de VS?', 'options' => ['De Mexicaanse Revolutie', 'De Cubaanse Revolutie', 'De Braziliaanse Revolutie', 'De Argentijnse Revolutie'], 'correct' => 'De Cubaanse Revolutie'],
                        ['id' => 17, 'question' => 'Wat was de naam van het economische hulpprogramma waarmee de VS West-Europa na WOII weer opbouwde?', 'options' => ['Truman-doctrine', 'Marshallplan', 'Molotov-plan', 'Eisenhower-doel'], 'correct' => 'Marshallplan'],
                        ['id' => 18, 'question' => 'In welk jaar viel de Sovjet-Unie (USSR) definitief uit elkaar?', 'options' => ['1989', '1990', '1991', '1993'], 'correct' => '1991'],
                        ['id' => 19, 'question' => 'Wie was de Zuid-Afrikaanse activist en president die decennialang gevangen zat vanwege apartheid en in 1994 president werd?', 'options' => ['Desmond Tutu', 'Nelson Mandela', 'Thabo Mbeki', 'F.W. de Klerk'], 'correct' => 'Nelson Mandela'],
                        ['id' => 20, 'question' => 'Welk verdrag in 1992 legde de officiële basis voor de oprichting van de Europese Unie (EU)?', 'options' => ['Verdrag van Maastricht', 'Verdrag van Rome', 'Verdrag van Lissabon', 'Verdrag van Nice'], 'correct' => 'Verdrag van Maastricht']
                    ]
                ],
                3 => [
                    'title' => 'Variant 3: 20e Eeuw & Conflicten',
                    'questions' => [
                        ['id' => 1, 'question' => 'Welk land werd in 1939 binnengevallen door nazi-Duitsland, wat de directe aanleiding was voor WOII in Europa?', 'options' => ['Frankrijk', 'Polen', 'Tsjecho-Slowakije', 'België'], 'correct' => 'Polen'],
                        ['id' => 2, 'question' => 'Hoe heette de geallieerde invasie in Normandië op 6 juni 1944?', 'options' => ['Operation Market Garden', 'D-Day (Operation Overlord)', 'Operation Barbarossa', 'Battle of the Bulge'], 'correct' => 'D-Day (Operation Overlord)'],
                        ['id' => 3, 'question' => 'Welke steden in Japan werden in augustus 1945 getroffen door Amerikaanse atoombommen?', 'options' => ['Tokyo en Kyoto', 'Hiroshima en Nagasaki', 'Osaka en Kobe', 'Yokohama en Nagoya'], 'correct' => 'Hiroshima en Nagasaki'],
                        ['id' => 4, 'question' => 'Hoe werd de ideologische en politieke spanning tussen de VS en de Sovjet-Unie na WOII genoemd?', 'options' => ['De Koude Oorlog', 'De Hete Oorlog', 'De Proxy Strijd', 'Het IJzeren Tijdperk'], 'correct' => 'De Koude Oorlog'],
                        ['id' => 5, 'question' => 'In welk decennium vond de Vietnamoorlog voor het grootste deel plaats voor Amerikaanse troepen?', 'options' => ['Jaren 1950', 'Jaren 1960 en 1970', 'Jaren 1980', 'Jaren 1940'], 'correct' => 'Jaren 1960 en 1970'],
                        ['id' => 6, 'question' => 'Wie was de Britse premier tijdens de Tweede Wereldoorlog die bekend stond om zijn doorzettingsvermogen?', 'options' => ['Neville Chamberlain', 'Winston Churchill', 'Clement Attlee', 'Harold Macmillan'], 'correct' => 'Winston Churchill'],
                        ['id' => 7, 'question' => 'Welke organisatie van westerse landen werd in 1949 opgericht als militaire bondgenootschap tegen de Sovjet-Unie?', 'options' => ['Warschaupact', 'NAVO (NATO)', 'Europese Unie', 'UNESCO'], 'correct' => 'NAVO (NATO)'],
                        ['id' => 8, 'question' => 'Wat was de tegenhanger van de NAVO vanuit het oostblok?', 'options' => ['Het Warschaupact', 'De Kominform', 'De Comecon', 'De Sovjet-Unie Liga'], 'correct' => 'Het Warschaupact'],
                        ['id' => 9, 'question' => 'In welk jaar werd de Staat Israël uitgeroepen, wat leidde tot het eerste Arabisch-Israëlische conflict?', 'options' => ['1945', '1948', '1956', '1967'], 'correct' => '1948'],
                        ['id' => 10, 'question' => 'Welke oorlog in 1967 duurde slechts zes dagen en eindigde in een grote strategische overwinning voor Israël?', 'options' => ['Jom Kipoeroorlog', 'Zesdaagse Oorlog', 'Suez-crisis', 'Libanonoorlog'], 'correct' => 'Zesdaagse Oorlog'],
                        ['id' => 11, 'question' => 'Wie was de leider van de Chinese Communistische Partij die in 1949 de Volksrepubliek China uitriep?', 'options' => ['Sun Yat-sen', 'Chiang Kai-shek', 'Mao Zedong', 'Deng Xiaoping'], 'correct' => 'Mao Zedong'],
                        ['id' => 12, 'question' => 'Wat was de naam van de beruchte slaven- en vernietigingskampencomplex in Polen gebouwd door nazi-Duitsland?', 'options' => ['Dachau', 'Auschwitz-Birkenau', 'Buchenwald', 'Treblinka'], 'correct' => 'Auschwitz-Birkenau'],
                        ['id' => 13, 'question' => 'In welk jaar werd de apartheid in Zuid-Afrika officieel afgeschaft?', 'options' => ['1985', '1990', '1994', '1999'], 'correct' => '1994'],
                        ['id' => 14, 'question' => 'Wie was de Amerikaanse burgerrechtenactivist die in 1963 zijn beroemde "I Have a Dream"-toespraak hield?', 'options' => ['Malcolm X', 'Martin Luther King Jr.', 'Rosa Parks', 'John Lewis'], 'correct' => 'Martin Luther King Jr.'],
                        ['id' => 15, 'question' => 'Welke crisis in 1956 betrof de nationalisatie van een belangrijke doorvaartroute door de Egyptische president Nasser?', 'options' => ['De Panamakanaal-crisis', 'De Suezcrisis', 'De Bosporuscrisis', 'De Straat van Hormuz-crisis'], 'correct' => 'De Suezcrisis'],
                        ['id' => 16, 'question' => 'In welk jaar vond de islamitische revolutie in Iran plaats waarbij de Sjah werd verdreven?', 'options' => ['1975', '1979', '1981', '1985'], 'correct' => '1979'],
                        ['id' => 17, 'question' => 'Welke oorlog tussen Iran en Irak woedde het grootste deel van de jaren 80?', 'options' => ['Golfoorlog', 'Iran-Irakoorlog', 'Mesopotamië-oorlog', 'Kaspische Oorlog'], 'correct' => 'Iran-Irakoorlog'],
                        ['id' => 18, 'question' => 'In welk jaar vond de Golfoorlog plaats na de Iraakse invasie van Koeweit?', 'options' => ['1988', '1990–1991', '1995', '2001'], 'correct' => '1990–1991'],
                        ['id' => 19, 'question' => 'Welke aanslagen op 11 september 2001 veranderden de wereldpolitiek en veiligheidsstructuur ingrijpend?', 'options' => ['Aanslagen op het Pentagon en World Trade Center', 'Aanslagen in Londen', 'Aanslagen in Madrid', 'Aanslagen in Parijs'], 'correct' => 'Aanslagen op het Pentagon en World Trade Center'],
                        ['id' => 20, 'question' => 'Hoe heette de laatste leider van de Sovjet-Unie wiens hervormingen (Glasnost en Perestroika) leidden tot het einde van de Koude Oorlog?', 'options' => ['Leonid Brezjnev', 'Michail Gorbatsjov', 'Boris Jeltsin', 'Joeri Andropov'], 'correct' => 'Michail Gorbatsjov']
                    ]
                ]
            ]
        ]
    ];

    #[Route('/quiz', name: 'app_quiz')]
    public function selectTopic(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $username = $session->get('user');
        if (!$username) {
            return $this->redirectToRoute('app_login');
        }

        $scoreRepository = $entityManager->getRepository(Score::class);
        $userScores = $scoreRepository->findBy(['username' => $username]);

        // Controleer per categorie welke varianten al gespeeld zijn en tel punten op
        $categoriesStatus = [];

        foreach ($this->categories as $key => $cat) {
            $variantsCompleted = 0;
            $totalPointsCat = 0;
            $variantDetails = [];

            foreach ($cat['variants'] as $vNum => $vData) {
                // Unieke gameName in DB, bijv. "Quiz: Algemene Kennis (Variant 1)"
                $gameIdentifier = 'Quiz: ' . $cat['title'] . ' (' . $vData['title'] . ')';
                
                // Zoek of de gebruiker deze heeft gespeeld
                $played = false;
                $pointsEarned = 0;
                foreach ($userScores as $score) {
                    if ($score->getGameName() === $gameIdentifier) {
                        $played = true;
                        $pointsEarned = $score->getPoints();
                        $totalPointsCat += $pointsEarned;
                        break;
                    }
                }

                if ($played) {
                    $variantsCompleted++;
                }

                $variantDetails[$vNum] = [
                    'title' => $vData['title'],
                    'played' => $played,
                    'points' => $pointsEarned
                ];
            }

            $categoriesStatus[$key] = [
                'title' => $cat['title'],
                'description' => $cat['description'],
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'variants' => $variantDetails,
                'completedAll' => ($variantsCompleted === 3), // Is de hele quiz (alle 3 varianten) klaar?
                'totalPoints' => $totalPointsCat
            ];
        }

        return $this->render('quiz/select.html.twig', [
            'categoriesStatus' => $categoriesStatus,
            'username' => $username
        ]);
    }

    #[Route('/quiz/play/{topic}/{variant}', name: 'app_quiz_play')]
    public function play(string $topic, int $variant, Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $username = $session->get('user');
        if (!$username) {
            return $this->redirectToRoute('app_login');
        }

        if (!isset($this->categories[$topic]) || !isset($this->categories[$topic]['variants'][$variant])) {
            return $this->redirectToRoute('app_quiz');
        }

        $cat = $this->categories[$topic];
        $variantData = $cat['variants'][$variant];
        $gameIdentifier = 'Quiz: ' . $cat['title'] . ' (' . $variantData['title'] . ')';

        // Check of deze variant al gespeeld is (Anti-cheat: niet opnieuw te maken!)
        $existingScore = $entityManager->getRepository(Score::class)->findOneBy([
            'username' => $username,
            'gameName' => $gameIdentifier
        ]);

        if ($existingScore) {
            $this->addFlash('error', 'Je hebt deze variant al voltooid en kunt hem niet opnieuw maken!');
            return $this->redirectToRoute('app_quiz');
        }

        $questions = $variantData['questions'];

        if ($request->isMethod('POST')) {
            $scoreEarned = 0;

            foreach ($questions as $q) {
                $userAnswer = $request->request->get('q_' . $q['id']);
                if ($userAnswer === $q['correct']) {
                    $scoreEarned += 5; // 5 punten per goed antwoord (20 vragen x 5 = 100 max per variant)
                }
            }

            // Sla op in MariaDB
            $score = new Score();
            $score->setUsername($username);
            $score->setGameName($gameIdentifier);
            $score->setPoints($scoreEarned);
            $score->setPlayedAt(new \DateTime());

            $entityManager->persist($score);
            $entityManager->flush();

            return $this->render('quiz/result.html.twig', [
                'scoreEarned' => $scoreEarned,
                'totalQuestions' => count($questions),
                'username' => $username,
                'quizTitle' => $cat['title'] . ' - ' . $variantData['title']
            ]);
        }

        return $this->render('quiz/play.html.twig', [
            'topicKey' => $topic,
            'variantNum' => $variant,
            'quizTitle' => $cat['title'] . ' - ' . $variantData['title'],
            'questions' => $questions,
            'username' => $username
        ]);
    }
}