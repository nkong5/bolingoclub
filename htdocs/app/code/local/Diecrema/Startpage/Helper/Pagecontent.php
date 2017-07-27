<?php

class Diecrema_Startpage_Helper_Pagecontent extends Mage_Core_Helper_Abstract
{

    public function pagecontent ()
    {
        $resourceModel = Mage::getResourceModel('diecrema_startpage/startpage');
        $code = Mage::app()->getStore()->getCode();
        if (!$data = $resourceModel->getPoster($code)) {
            $data = array(
              'teaser_1_large' => 'teaser_1_large_default.png',
              'teaser_1_small_txt' => 'teaser_1_small_txt_default.png',
              'teaser_1_small' => 'teaser_1_small_default.png',
              'teaser_1_large_above_txt' => 'Rocket R58 Dualboiler',
              'teaser_1_large_below_txt' => 'Nur 2.200,00 €',
              'teaser_1_link' => '/espressomaschinen/rocket-espresso/rocket-r58-dual-boiler.html',

              'teaser_2_large' => 'teaser_2_large_default.png',
              'teaser_2_small_txt' => 'teaser_2_small_txt_default.png',
              'teaser_2_small' => 'teaser_2_small_default.png',
              'teaser_2_large_above_txt' => 'La Marzocco GS/3',
              'teaser_2_large_below_txt' => 'Nur 5.690,00 €',
              'teaser_2_link' => '/espressomaschinen/la-marzocco/la-marzocco-gs-3-mit-paddle-und-seitenteilen-aus-glas.html',

              'teaser_3_large' => 'teaser_3_large_default.png',
              'teaser_3_small_txt' => 'teaser_3_small_txt_default.png',
              'teaser_3_small' => 'teaser_3_small_default.png',
              'teaser_3_large_above_txt' => 'Showroom Rhöndorf',
              'teaser_3_large_below_txt' => '',
              'teaser_3_link' => '/showroom',

              'intro_txt_title' => 'DieCrema.de - Ihr Shop für Espressomaschinen und Kaffee',
              'intro_txt' =>
                       'Wir heißen Sie herzlich willkommen in unserem DieCrema Online-Shop. Bei uns finden Sie alles rund um das Thema Espressomaschinen, Kaffeemaschinen, Kaffeemühlen und Espresso–Sorten. Wir bieten Ihnen ausschließlich Ware von bester Qualität, denn wir beschäftigen uns seit einigen Jahren mit Kaffeemaschinen, Espressomaschinen und Kaffeemühlen und machen nun das Hobby zum Beruf.

                        <div class="diecrema_description_mehr">
                        <p class="pTop12 pBottom12">
                        Zudem sind wir autorisierter Fachhandels-Partner von namenhaften Herstellern wie z.B. Bezzera, Rocket Espresso, Mazzer, LaPavoni und Elektra. DieCrema steht für Qualität und Genuss. Espresso soll schmecken, jedoch gelingt dies nur mit der richtigen Espressomaschine. Daher bieten wir Ihnen eine große Auswahl von verschiedenen bekannten Espressomaschinen Herstellern an, damit Sie die passende Espressomaschine für ihren persönlichen Gebrauch finden. Stellen Sie sich vor, sie sitzen an einem sommerlichen Tag in Italien, in einem Straßencafé und trinken einen Espresso. Ein genussvoller Moment. Wir wollen Ihnen diesen Moment schenken. Mit unseren originalen Espresso-Sorten z.B. von danesi oder Brasil Oro oder unserem Bio Espresso aus Triest von Alessandro Hausbrandt, zusammen mit einer Bezzera Espressomaschine und einer Mazzer Kaffeemühle bringen wir Ihnen das Flair eines echten Italienischen Cafés direkt nach Hause. Durch die Mischung von liebevoll hergestellten Espresso-Sorten und den qualitativ hochwertig verarbeiteten Espressomaschinen, schmecken sie den Genuss förmlich heraus. Ob Espressomaschine oder Kaffeemühle, ob Bezzera, Elektra oder Rocket Espresso unsere Produkte haben alle etwas gemeinsam. Sie bestechen durch ihr fantastisches Design und durch ihre hervorragende Funktionalität! Unsere Espressomaschinen sind leicht zu bedienen. Elektra Siebträger der Elektra Linea Bar werden erfolgreich in der Gastronomie eingesetzt.
                        </p>
                        Als autorisierter Partner von den bekanntesten Espressomaschinen Herstellern arbeiten wir eng mit bekannten Kaffeeröstereien wie Danesi und BrasilOro oder ATT zusammen und wissen daher, dass die Qualitätsbestimmungen den höchsten Anforderungen entsprechen. Wir vertrauen auf unsere Produkte, da bei der Herstellung großer Wert auf Qualität gelegt wird. Die Danesi und BrasilOro Kaffeebohnen werden sorgfältig ausgewählt, der Röstungsprozess wird korrekt durchgeführt und die perfekte Mischung des Aromas wird beigesetzt. Aus diesen Gründen können wir die Produkte gewissenhaft vertreiben. Probieren sie Kaffee/ Espresso-Sorten aus unserem Sortiment. Auch unseren ATT Bio-Espresso, von leicht bis hin zu stark gerösteten Espressobohnen ist alles dabei.
                        </div>

                        <a href="#" class="more">mehr...</a>',
            );
        }
        return $data;
    }

}


