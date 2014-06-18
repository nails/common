<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| LANGUAGES
|--------------------------------------------------------------------------
|
| This config file contains an ISO 639-2 list of languages.
|
*/

//	The default language for this app, If using anything other than English
//	ensure there are language files available (incluidng Nails lang files
//	and CodeIgniter lang files).

$config['languages_default'] = 'english';

// --------------------------------------------------------------------------

//	Define supported langauges for the site.
$config['languages_enabled'] = array( 'english' );

// --------------------------------------------------------------------------

//	Define the list of possible languages
$config['languages'] = array();

// --------------------------------------------------------------------------

//	English is a special case; due to CodeIgniter expecting the word english
//	(rather than the code 'eng') english must be specified in full.

$config['languages']['english']			= new stdClass();
$config['languages']['english']->code	= 'english';
$config['languages']['english']->label	= 'English';

// --------------------------------------------------------------------------

//	Let's also make special cases for other major langauges
$config['languages']['spanish']			= new stdClass();
$config['languages']['spanish']->code	= 'spanish';
$config['languages']['spanish']->label	= 'Spanish';

$config['languages']['french']			= new stdClass();
$config['languages']['french']->code	= 'french';
$config['languages']['french']->label	= 'French';

$config['languages']['german']			= new stdClass();
$config['languages']['german']->code	= 'german';
$config['languages']['german']->label	= 'German';

// --------------------------------------------------------------------------

$config['languages']['aar']			= new stdClass();
$config['languages']['aar']->code	= 'aar';
$config['languages']['aar']->label	= 'Afar';

$config['languages']['abk']			= new stdClass();
$config['languages']['abk']->code	= 'abk';
$config['languages']['abk']->label	= 'Abkhazian';

$config['languages']['ace']			= new stdClass();
$config['languages']['ace']->code	= 'ace';
$config['languages']['ace']->label	= 'Achinese';

$config['languages']['ach']			= new stdClass();
$config['languages']['ach']->code	= 'ach';
$config['languages']['ach']->label	= 'Acoli';

$config['languages']['ada']			= new stdClass();
$config['languages']['ada']->code	= 'ada';
$config['languages']['ada']->label	= 'Adangme';

$config['languages']['ady']			= new stdClass();
$config['languages']['ady']->code	= 'ady';
$config['languages']['ady']->label	= 'Adyghe; Adygei';

$config['languages']['afa']			= new stdClass();
$config['languages']['afa']->code	= 'afa';
$config['languages']['afa']->label	= 'Afro-Asiatic languages';

$config['languages']['afh']			= new stdClass();
$config['languages']['afh']->code	= 'afh';
$config['languages']['afh']->label	= 'Afrihili';

$config['languages']['afr']			= new stdClass();
$config['languages']['afr']->code	= 'afr';
$config['languages']['afr']->label	= 'Afrikaans';

$config['languages']['ain']			= new stdClass();
$config['languages']['ain']->code	= 'ain';
$config['languages']['ain']->label	= 'Ainu';

$config['languages']['aka']			= new stdClass();
$config['languages']['aka']->code	= 'aka';
$config['languages']['aka']->label	= 'Akan';

$config['languages']['akk']			= new stdClass();
$config['languages']['akk']->code	= 'akk';
$config['languages']['akk']->label	= 'Akkadian';

$config['languages']['alb']			= new stdClass();
$config['languages']['alb']->code	= 'alb';
$config['languages']['alb']->label	= 'Albanian';

$config['languages']['ale']			= new stdClass();
$config['languages']['ale']->code	= 'ale';
$config['languages']['ale']->label	= 'Aleut';

$config['languages']['alg']			= new stdClass();
$config['languages']['alg']->code	= 'alg';
$config['languages']['alg']->label	= 'Algonquian languages';

$config['languages']['alt']			= new stdClass();
$config['languages']['alt']->code	= 'alt';
$config['languages']['alt']->label	= 'Southern Altai';

$config['languages']['amh']			= new stdClass();
$config['languages']['amh']->code	= 'amh';
$config['languages']['amh']->label	= 'Amharic';

$config['languages']['anp']			= new stdClass();
$config['languages']['anp']->code	= 'anp';
$config['languages']['anp']->label	= 'Angika';

$config['languages']['apa']			= new stdClass();
$config['languages']['apa']->code	= 'apa';
$config['languages']['apa']->label	= 'Apache languages';

$config['languages']['ara']			= new stdClass();
$config['languages']['ara']->code	= 'ara';
$config['languages']['ara']->label	= 'Arabic';

$config['languages']['arg']			= new stdClass();
$config['languages']['arg']->code	= 'arg';
$config['languages']['arg']->label	= 'Aragonese';

$config['languages']['arm']			= new stdClass();
$config['languages']['arm']->code	= 'arm';
$config['languages']['arm']->label	= 'Armenian';

$config['languages']['arn']			= new stdClass();
$config['languages']['arn']->code	= 'arn';
$config['languages']['arn']->label	= 'Mapudungun; Mapuche';

$config['languages']['arp']			= new stdClass();
$config['languages']['arp']->code	= 'arp';
$config['languages']['arp']->label	= 'Arapaho';

$config['languages']['art']			= new stdClass();
$config['languages']['art']->code	= 'art';
$config['languages']['art']->label	= 'Artificial languages';

$config['languages']['arw']			= new stdClass();
$config['languages']['arw']->code	= 'arw';
$config['languages']['arw']->label	= 'Arawak';

$config['languages']['asm']			= new stdClass();
$config['languages']['asm']->code	= 'asm';
$config['languages']['asm']->label	= 'Assamese';

$config['languages']['ast']			= new stdClass();
$config['languages']['ast']->code	= 'ast';
$config['languages']['ast']->label	= 'Asturian; Bable; Leonese; Asturleonese';

$config['languages']['ath']			= new stdClass();
$config['languages']['ath']->code	= 'ath';
$config['languages']['ath']->label	= 'Athapascan languages';

$config['languages']['aus']			= new stdClass();
$config['languages']['aus']->code	= 'aus';
$config['languages']['aus']->label	= 'Australian languages';

$config['languages']['ava']			= new stdClass();
$config['languages']['ava']->code	= 'ava';
$config['languages']['ava']->label	= 'Avaric';

$config['languages']['ave']			= new stdClass();
$config['languages']['ave']->code	= 'ave';
$config['languages']['ave']->label	= 'Avestan';

$config['languages']['awa']			= new stdClass();
$config['languages']['awa']->code	= 'awa';
$config['languages']['awa']->label	= 'Awadhi';

$config['languages']['aym']			= new stdClass();
$config['languages']['aym']->code	= 'aym';
$config['languages']['aym']->label	= 'Aymara';

$config['languages']['aze']			= new stdClass();
$config['languages']['aze']->code	= 'aze';
$config['languages']['aze']->label	= 'Azerbaijani';

$config['languages']['bad']			= new stdClass();
$config['languages']['bad']->code	= 'bad';
$config['languages']['bad']->label	= 'Banda languages';

$config['languages']['bai']			= new stdClass();
$config['languages']['bai']->code	= 'bai';
$config['languages']['bai']->label	= 'Bamileke languages';

$config['languages']['bak']			= new stdClass();
$config['languages']['bak']->code	= 'bak';
$config['languages']['bak']->label	= 'Bashkir';

$config['languages']['bal']			= new stdClass();
$config['languages']['bal']->code	= 'bal';
$config['languages']['bal']->label	= 'Baluchi';

$config['languages']['bam']			= new stdClass();
$config['languages']['bam']->code	= 'bam';
$config['languages']['bam']->label	= 'Bambara';

$config['languages']['ban']			= new stdClass();
$config['languages']['ban']->code	= 'ban';
$config['languages']['ban']->label	= 'Balinese';

$config['languages']['baq']			= new stdClass();
$config['languages']['baq']->code	= 'baq';
$config['languages']['baq']->label	= 'Basque';

$config['languages']['bas']			= new stdClass();
$config['languages']['bas']->code	= 'bas';
$config['languages']['bas']->label	= 'Basa';

$config['languages']['bat']			= new stdClass();
$config['languages']['bat']->code	= 'bat';
$config['languages']['bat']->label	= 'Baltic languages';

$config['languages']['bej']			= new stdClass();
$config['languages']['bej']->code	= 'bej';
$config['languages']['bej']->label	= 'Beja; Bedawiyet';

$config['languages']['bel']			= new stdClass();
$config['languages']['bel']->code	= 'bel';
$config['languages']['bel']->label	= 'Belarusian';

$config['languages']['bem']			= new stdClass();
$config['languages']['bem']->code	= 'bem';
$config['languages']['bem']->label	= 'Bemba';

$config['languages']['ben']			= new stdClass();
$config['languages']['ben']->code	= 'ben';
$config['languages']['ben']->label	= 'Bengali';

$config['languages']['ber']			= new stdClass();
$config['languages']['ber']->code	= 'ber';
$config['languages']['ber']->label	= 'Berber languages';

$config['languages']['bho']			= new stdClass();
$config['languages']['bho']->code	= 'bho';
$config['languages']['bho']->label	= 'Bhojpuri';

$config['languages']['bih']			= new stdClass();
$config['languages']['bih']->code	= 'bih';
$config['languages']['bih']->label	= 'Bihari languages';

$config['languages']['bik']			= new stdClass();
$config['languages']['bik']->code	= 'bik';
$config['languages']['bik']->label	= 'Bikol';

$config['languages']['bin']			= new stdClass();
$config['languages']['bin']->code	= 'bin';
$config['languages']['bin']->label	= 'Bini; Edo';

$config['languages']['bis']			= new stdClass();
$config['languages']['bis']->code	= 'bis';
$config['languages']['bis']->label	= 'Bislama';

$config['languages']['bla']			= new stdClass();
$config['languages']['bla']->code	= 'bla';
$config['languages']['bla']->label	= 'Siksika';

$config['languages']['bnt']			= new stdClass();
$config['languages']['bnt']->code	= 'bnt';
$config['languages']['bnt']->label	= 'Bantu (Other)';

$config['languages']['bos']			= new stdClass();
$config['languages']['bos']->code	= 'bos';
$config['languages']['bos']->label	= 'Bosnian';

$config['languages']['bra']			= new stdClass();
$config['languages']['bra']->code	= 'bra';
$config['languages']['bra']->label	= 'Braj';

$config['languages']['bre']			= new stdClass();
$config['languages']['bre']->code	= 'bre';
$config['languages']['bre']->label	= 'Breton';

$config['languages']['btk']			= new stdClass();
$config['languages']['btk']->code	= 'btk';
$config['languages']['btk']->label	= 'Batak languages';

$config['languages']['bua']			= new stdClass();
$config['languages']['bua']->code	= 'bua';
$config['languages']['bua']->label	= 'Buriat';

$config['languages']['bug']			= new stdClass();
$config['languages']['bug']->code	= 'bug';
$config['languages']['bug']->label	= 'Buginese';

$config['languages']['bul']			= new stdClass();
$config['languages']['bul']->code	= 'bul';
$config['languages']['bul']->label	= 'Bulgarian';

$config['languages']['bur']			= new stdClass();
$config['languages']['bur']->code	= 'bur';
$config['languages']['bur']->label	= 'Burmese';

$config['languages']['byn']			= new stdClass();
$config['languages']['byn']->code	= 'byn';
$config['languages']['byn']->label	= 'Blin; Bilin';

$config['languages']['cad']			= new stdClass();
$config['languages']['cad']->code	= 'cad';
$config['languages']['cad']->label	= 'Caddo';

$config['languages']['cai']			= new stdClass();
$config['languages']['cai']->code	= 'cai';
$config['languages']['cai']->label	= 'Central American Indian languages';

$config['languages']['car']			= new stdClass();
$config['languages']['car']->code	= 'car';
$config['languages']['car']->label	= 'Galibi Carib';

$config['languages']['cat']			= new stdClass();
$config['languages']['cat']->code	= 'cat';
$config['languages']['cat']->label	= 'Catalan; Valencian';

$config['languages']['cau']			= new stdClass();
$config['languages']['cau']->code	= 'cau';
$config['languages']['cau']->label	= 'Caucasian languages';

$config['languages']['ceb']			= new stdClass();
$config['languages']['ceb']->code	= 'ceb';
$config['languages']['ceb']->label	= 'Cebuano';

$config['languages']['cel']			= new stdClass();
$config['languages']['cel']->code	= 'cel';
$config['languages']['cel']->label	= 'Celtic languages';

$config['languages']['cha']			= new stdClass();
$config['languages']['cha']->code	= 'cha';
$config['languages']['cha']->label	= 'Chamorro';

$config['languages']['chb']			= new stdClass();
$config['languages']['chb']->code	= 'chb';
$config['languages']['chb']->label	= 'Chibcha';

$config['languages']['che']			= new stdClass();
$config['languages']['che']->code	= 'che';
$config['languages']['che']->label	= 'Chechen';

$config['languages']['chg']			= new stdClass();
$config['languages']['chg']->code	= 'chg';
$config['languages']['chg']->label	= 'Chagatai';

$config['languages']['chi']			= new stdClass();
$config['languages']['chi']->code	= 'chi';
$config['languages']['chi']->label	= 'Chinese';

$config['languages']['chk']			= new stdClass();
$config['languages']['chk']->code	= 'chk';
$config['languages']['chk']->label	= 'Chuukese';

$config['languages']['chm']			= new stdClass();
$config['languages']['chm']->code	= 'chm';
$config['languages']['chm']->label	= 'Mari';

$config['languages']['chn']			= new stdClass();
$config['languages']['chn']->code	= 'chn';
$config['languages']['chn']->label	= 'Chinook jargon';

$config['languages']['cho']			= new stdClass();
$config['languages']['cho']->code	= 'cho';
$config['languages']['cho']->label	= 'Choctaw';

$config['languages']['chp']			= new stdClass();
$config['languages']['chp']->code	= 'chp';
$config['languages']['chp']->label	= 'Chipewyan; Dene Suline';

$config['languages']['chr']			= new stdClass();
$config['languages']['chr']->code	= 'chr';
$config['languages']['chr']->label	= 'Cherokee';

$config['languages']['chu']			= new stdClass();
$config['languages']['chu']->code	= 'chu';
$config['languages']['chu']->label	= 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic';

$config['languages']['chv']			= new stdClass();
$config['languages']['chv']->code	= 'chv';
$config['languages']['chv']->label	= 'Chuvash';

$config['languages']['chy']			= new stdClass();
$config['languages']['chy']->code	= 'chy';
$config['languages']['chy']->label	= 'Cheyenne';

$config['languages']['cmc']			= new stdClass();
$config['languages']['cmc']->code	= 'cmc';
$config['languages']['cmc']->label	= 'Chamic languages';

$config['languages']['cop']			= new stdClass();
$config['languages']['cop']->code	= 'cop';
$config['languages']['cop']->label	= 'Coptic';

$config['languages']['cor']			= new stdClass();
$config['languages']['cor']->code	= 'cor';
$config['languages']['cor']->label	= 'Cornish';

$config['languages']['cos']			= new stdClass();
$config['languages']['cos']->code	= 'cos';
$config['languages']['cos']->label	= 'Corsican';

$config['languages']['cpe']			= new stdClass();
$config['languages']['cpe']->code	= 'cpe';
$config['languages']['cpe']->label	= 'Creoles and pidgins, English based';

$config['languages']['cpf']			= new stdClass();
$config['languages']['cpf']->code	= 'cpf';
$config['languages']['cpf']->label	= 'Creoles and pidgins, French-based ';

$config['languages']['cpp']			= new stdClass();
$config['languages']['cpp']->code	= 'cpp';
$config['languages']['cpp']->label	= 'Creoles and pidgins, Portuguese-based ';

$config['languages']['cre']			= new stdClass();
$config['languages']['cre']->code	= 'cre';
$config['languages']['cre']->label	= 'Cree';

$config['languages']['crh']			= new stdClass();
$config['languages']['crh']->code	= 'crh';
$config['languages']['crh']->label	= 'Crimean Tatar; Crimean Turkish';

$config['languages']['crp']			= new stdClass();
$config['languages']['crp']->code	= 'crp';
$config['languages']['crp']->label	= 'Creoles and pidgins ';

$config['languages']['csb']			= new stdClass();
$config['languages']['csb']->code	= 'csb';
$config['languages']['csb']->label	= 'Kashubian';

$config['languages']['cus']			= new stdClass();
$config['languages']['cus']->code	= 'cus';
$config['languages']['cus']->label	= 'Cushitic languages';

$config['languages']['cze']			= new stdClass();
$config['languages']['cze']->code	= 'cze';
$config['languages']['cze']->label	= 'Czech';

$config['languages']['dak']			= new stdClass();
$config['languages']['dak']->code	= 'dak';
$config['languages']['dak']->label	= 'Dakota';

$config['languages']['dan']			= new stdClass();
$config['languages']['dan']->code	= 'dan';
$config['languages']['dan']->label	= 'Danish';

$config['languages']['dar']			= new stdClass();
$config['languages']['dar']->code	= 'dar';
$config['languages']['dar']->label	= 'Dargwa';

$config['languages']['day']			= new stdClass();
$config['languages']['day']->code	= 'day';
$config['languages']['day']->label	= 'Land Dayak languages';

$config['languages']['del']			= new stdClass();
$config['languages']['del']->code	= 'del';
$config['languages']['del']->label	= 'Delaware';

$config['languages']['den']			= new stdClass();
$config['languages']['den']->code	= 'den';
$config['languages']['den']->label	= 'Slave (Athapascan)';

$config['languages']['dgr']			= new stdClass();
$config['languages']['dgr']->code	= 'dgr';
$config['languages']['dgr']->label	= 'Dogrib';

$config['languages']['din']			= new stdClass();
$config['languages']['din']->code	= 'din';
$config['languages']['din']->label	= 'Dinka';

$config['languages']['div']			= new stdClass();
$config['languages']['div']->code	= 'div';
$config['languages']['div']->label	= 'Divehi; Dhivehi; Maldivian';

$config['languages']['doi']			= new stdClass();
$config['languages']['doi']->code	= 'doi';
$config['languages']['doi']->label	= 'Dogri';

$config['languages']['dra']			= new stdClass();
$config['languages']['dra']->code	= 'dra';
$config['languages']['dra']->label	= 'Dravidian languages';

$config['languages']['dsb']			= new stdClass();
$config['languages']['dsb']->code	= 'dsb';
$config['languages']['dsb']->label	= 'Lower Sorbian';

$config['languages']['dua']			= new stdClass();
$config['languages']['dua']->code	= 'dua';
$config['languages']['dua']->label	= 'Duala';

$config['languages']['dut']			= new stdClass();
$config['languages']['dut']->code	= 'dut';
$config['languages']['dut']->label	= 'Dutch; Flemish';

$config['languages']['dyu']			= new stdClass();
$config['languages']['dyu']->code	= 'dyu';
$config['languages']['dyu']->label	= 'Dyula';

$config['languages']['dzo']			= new stdClass();
$config['languages']['dzo']->code	= 'dzo';
$config['languages']['dzo']->label	= 'Dzongkha';

$config['languages']['efi']			= new stdClass();
$config['languages']['efi']->code	= 'efi';
$config['languages']['efi']->label	= 'Efik';

$config['languages']['egy']			= new stdClass();
$config['languages']['egy']->code	= 'egy';
$config['languages']['egy']->label	= 'Egyptian (Ancient)';

$config['languages']['eka']			= new stdClass();
$config['languages']['eka']->code	= 'eka';
$config['languages']['eka']->label	= 'Ekajuk';

$config['languages']['elx']			= new stdClass();
$config['languages']['elx']->code	= 'elx';
$config['languages']['elx']->label	= 'Elamite';

$config['languages']['epo']			= new stdClass();
$config['languages']['epo']->code	= 'epo';
$config['languages']['epo']->label	= 'Esperanto';

$config['languages']['est']			= new stdClass();
$config['languages']['est']->code	= 'est';
$config['languages']['est']->label	= 'Estonian';

$config['languages']['ewe']			= new stdClass();
$config['languages']['ewe']->code	= 'ewe';
$config['languages']['ewe']->label	= 'Ewe';

$config['languages']['ewo']			= new stdClass();
$config['languages']['ewo']->code	= 'ewo';
$config['languages']['ewo']->label	= 'Ewondo';

$config['languages']['fan']			= new stdClass();
$config['languages']['fan']->code	= 'fan';
$config['languages']['fan']->label	= 'Fang';

$config['languages']['fao']			= new stdClass();
$config['languages']['fao']->code	= 'fao';
$config['languages']['fao']->label	= 'Faroese';

$config['languages']['fat']			= new stdClass();
$config['languages']['fat']->code	= 'fat';
$config['languages']['fat']->label	= 'Fanti';

$config['languages']['fij']			= new stdClass();
$config['languages']['fij']->code	= 'fij';
$config['languages']['fij']->label	= 'Fijian';

$config['languages']['fil']			= new stdClass();
$config['languages']['fil']->code	= 'fil';
$config['languages']['fil']->label	= 'Filipino; Pilipino';

$config['languages']['fin']			= new stdClass();
$config['languages']['fin']->code	= 'fin';
$config['languages']['fin']->label	= 'Finnish';

$config['languages']['fiu']			= new stdClass();
$config['languages']['fiu']->code	= 'fiu';
$config['languages']['fiu']->label	= 'Finno-Ugrian languages';

$config['languages']['fon']			= new stdClass();
$config['languages']['fon']->code	= 'fon';
$config['languages']['fon']->label	= 'Fon';

$config['languages']['frr']			= new stdClass();
$config['languages']['frr']->code	= 'frr';
$config['languages']['frr']->label	= 'Northern Frisian';

$config['languages']['frs']			= new stdClass();
$config['languages']['frs']->code	= 'frs';
$config['languages']['frs']->label	= 'Eastern Frisian';

$config['languages']['fry']			= new stdClass();
$config['languages']['fry']->code	= 'fry';
$config['languages']['fry']->label	= 'Western Frisian';

$config['languages']['ful']			= new stdClass();
$config['languages']['ful']->code	= 'ful';
$config['languages']['ful']->label	= 'Fulah';

$config['languages']['fur']			= new stdClass();
$config['languages']['fur']->code	= 'fur';
$config['languages']['fur']->label	= 'Friulian';

$config['languages']['gaa']			= new stdClass();
$config['languages']['gaa']->code	= 'gaa';
$config['languages']['gaa']->label	= 'Ga';

$config['languages']['gay']			= new stdClass();
$config['languages']['gay']->code	= 'gay';
$config['languages']['gay']->label	= 'Gayo';

$config['languages']['gba']			= new stdClass();
$config['languages']['gba']->code	= 'gba';
$config['languages']['gba']->label	= 'Gbaya';

$config['languages']['gem']			= new stdClass();
$config['languages']['gem']->code	= 'gem';
$config['languages']['gem']->label	= 'Germanic languages';

$config['languages']['geo']			= new stdClass();
$config['languages']['geo']->code	= 'geo';
$config['languages']['geo']->label	= 'Georgian';

$config['languages']['gez']			= new stdClass();
$config['languages']['gez']->code	= 'gez';
$config['languages']['gez']->label	= 'Geez';

$config['languages']['gil']			= new stdClass();
$config['languages']['gil']->code	= 'gil';
$config['languages']['gil']->label	= 'Gilbertese';

$config['languages']['gla']			= new stdClass();
$config['languages']['gla']->code	= 'gla';
$config['languages']['gla']->label	= 'Gaelic; Scottish Gaelic';

$config['languages']['gle']			= new stdClass();
$config['languages']['gle']->code	= 'gle';
$config['languages']['gle']->label	= 'Irish';

$config['languages']['glg']			= new stdClass();
$config['languages']['glg']->code	= 'glg';
$config['languages']['glg']->label	= 'Galician';

$config['languages']['glv']			= new stdClass();
$config['languages']['glv']->code	= 'glv';
$config['languages']['glv']->label	= 'Manx';

$config['languages']['gon']			= new stdClass();
$config['languages']['gon']->code	= 'gon';
$config['languages']['gon']->label	= 'Gondi';

$config['languages']['gor']			= new stdClass();
$config['languages']['gor']->code	= 'gor';
$config['languages']['gor']->label	= 'Gorontalo';

$config['languages']['got']			= new stdClass();
$config['languages']['got']->code	= 'got';
$config['languages']['got']->label	= 'Gothic';

$config['languages']['grb']			= new stdClass();
$config['languages']['grb']->code	= 'grb';
$config['languages']['grb']->label	= 'Grebo';

$config['languages']['grc']			= new stdClass();
$config['languages']['grc']->code	= 'grc';
$config['languages']['grc']->label	= 'Greek, Ancient (to 1453)';

$config['languages']['gre']			= new stdClass();
$config['languages']['gre']->code	= 'gre';
$config['languages']['gre']->label	= 'Greek, Modern (1453-)';

$config['languages']['grn']			= new stdClass();
$config['languages']['grn']->code	= 'grn';
$config['languages']['grn']->label	= 'Guarani';

$config['languages']['gsw']			= new stdClass();
$config['languages']['gsw']->code	= 'gsw';
$config['languages']['gsw']->label	= 'Swiss German; Alemannic; Alsatian';

$config['languages']['guj']			= new stdClass();
$config['languages']['guj']->code	= 'guj';
$config['languages']['guj']->label	= 'Gujarati';

$config['languages']['gwi']			= new stdClass();
$config['languages']['gwi']->code	= 'gwi';
$config['languages']['gwi']->label	= 'Gwich\'in';

$config['languages']['hai']			= new stdClass();
$config['languages']['hai']->code	= 'hai';
$config['languages']['hai']->label	= 'Haida';

$config['languages']['hat']			= new stdClass();
$config['languages']['hat']->code	= 'hat';
$config['languages']['hat']->label	= 'Haitian; Haitian Creole';

$config['languages']['hau']			= new stdClass();
$config['languages']['hau']->code	= 'hau';
$config['languages']['hau']->label	= 'Hausa';

$config['languages']['haw']			= new stdClass();
$config['languages']['haw']->code	= 'haw';
$config['languages']['haw']->label	= 'Hawaiian';

$config['languages']['heb']			= new stdClass();
$config['languages']['heb']->code	= 'heb';
$config['languages']['heb']->label	= 'Hebrew';

$config['languages']['her']			= new stdClass();
$config['languages']['her']->code	= 'her';
$config['languages']['her']->label	= 'Herero';

$config['languages']['hil']			= new stdClass();
$config['languages']['hil']->code	= 'hil';
$config['languages']['hil']->label	= 'Hiligaynon';

$config['languages']['him']			= new stdClass();
$config['languages']['him']->code	= 'him';
$config['languages']['him']->label	= 'Himachali languages; Western Pahari languages';

$config['languages']['hin']			= new stdClass();
$config['languages']['hin']->code	= 'hin';
$config['languages']['hin']->label	= 'Hindi';

$config['languages']['hit']			= new stdClass();
$config['languages']['hit']->code	= 'hit';
$config['languages']['hit']->label	= 'Hittite';

$config['languages']['hmn']			= new stdClass();
$config['languages']['hmn']->code	= 'hmn';
$config['languages']['hmn']->label	= 'Hmong; Mong';

$config['languages']['hmo']			= new stdClass();
$config['languages']['hmo']->code	= 'hmo';
$config['languages']['hmo']->label	= 'Hiri Motu';

$config['languages']['hrv']			= new stdClass();
$config['languages']['hrv']->code	= 'hrv';
$config['languages']['hrv']->label	= 'Croatian';

$config['languages']['hsb']			= new stdClass();
$config['languages']['hsb']->code	= 'hsb';
$config['languages']['hsb']->label	= 'Upper Sorbian';

$config['languages']['hun']			= new stdClass();
$config['languages']['hun']->code	= 'hun';
$config['languages']['hun']->label	= 'Hungarian';

$config['languages']['hup']			= new stdClass();
$config['languages']['hup']->code	= 'hup';
$config['languages']['hup']->label	= 'Hupa';

$config['languages']['iba']			= new stdClass();
$config['languages']['iba']->code	= 'iba';
$config['languages']['iba']->label	= 'Iban';

$config['languages']['ibo']			= new stdClass();
$config['languages']['ibo']->code	= 'ibo';
$config['languages']['ibo']->label	= 'Igbo';

$config['languages']['ice']			= new stdClass();
$config['languages']['ice']->code	= 'ice';
$config['languages']['ice']->label	= 'Icelandic';

$config['languages']['ido']			= new stdClass();
$config['languages']['ido']->code	= 'ido';
$config['languages']['ido']->label	= 'Ido';

$config['languages']['iii']			= new stdClass();
$config['languages']['iii']->code	= 'iii';
$config['languages']['iii']->label	= 'Sichuan Yi; Nuosu';

$config['languages']['ijo']			= new stdClass();
$config['languages']['ijo']->code	= 'ijo';
$config['languages']['ijo']->label	= 'Ijo languages';

$config['languages']['iku']			= new stdClass();
$config['languages']['iku']->code	= 'iku';
$config['languages']['iku']->label	= 'Inuktitut';

$config['languages']['ile']			= new stdClass();
$config['languages']['ile']->code	= 'ile';
$config['languages']['ile']->label	= 'Interlingue; Occidental';

$config['languages']['ilo']			= new stdClass();
$config['languages']['ilo']->code	= 'ilo';
$config['languages']['ilo']->label	= 'Iloko';

$config['languages']['inc']			= new stdClass();
$config['languages']['inc']->code	= 'inc';
$config['languages']['inc']->label	= 'Indic languages';

$config['languages']['ind']			= new stdClass();
$config['languages']['ind']->code	= 'ind';
$config['languages']['ind']->label	= 'Indonesian';

$config['languages']['ine']			= new stdClass();
$config['languages']['ine']->code	= 'ine';
$config['languages']['ine']->label	= 'Indo-European languages';

$config['languages']['inh']			= new stdClass();
$config['languages']['inh']->code	= 'inh';
$config['languages']['inh']->label	= 'Ingush';

$config['languages']['ipk']			= new stdClass();
$config['languages']['ipk']->code	= 'ipk';
$config['languages']['ipk']->label	= 'Inupiaq';

$config['languages']['ira']			= new stdClass();
$config['languages']['ira']->code	= 'ira';
$config['languages']['ira']->label	= 'Iranian languages';

$config['languages']['iro']			= new stdClass();
$config['languages']['iro']->code	= 'iro';
$config['languages']['iro']->label	= 'Iroquoian languages';

$config['languages']['ita']			= new stdClass();
$config['languages']['ita']->code	= 'ita';
$config['languages']['ita']->label	= 'Italian';

$config['languages']['jav']			= new stdClass();
$config['languages']['jav']->code	= 'jav';
$config['languages']['jav']->label	= 'Javanese';

$config['languages']['jbo']			= new stdClass();
$config['languages']['jbo']->code	= 'jbo';
$config['languages']['jbo']->label	= 'Lojban';

$config['languages']['jpn']			= new stdClass();
$config['languages']['jpn']->code	= 'jpn';
$config['languages']['jpn']->label	= 'Japanese';

$config['languages']['jpr']			= new stdClass();
$config['languages']['jpr']->code	= 'jpr';
$config['languages']['jpr']->label	= 'Judeo-Persian';

$config['languages']['jrb']			= new stdClass();
$config['languages']['jrb']->code	= 'jrb';
$config['languages']['jrb']->label	= 'Judeo-Arabic';

$config['languages']['kaa']			= new stdClass();
$config['languages']['kaa']->code	= 'kaa';
$config['languages']['kaa']->label	= 'Kara-Kalpak';

$config['languages']['kab']			= new stdClass();
$config['languages']['kab']->code	= 'kab';
$config['languages']['kab']->label	= 'Kabyle';

$config['languages']['kac']			= new stdClass();
$config['languages']['kac']->code	= 'kac';
$config['languages']['kac']->label	= 'Kachin; Jingpho';

$config['languages']['kal']			= new stdClass();
$config['languages']['kal']->code	= 'kal';
$config['languages']['kal']->label	= 'Kalaallisut; Greenlandic';

$config['languages']['kam']			= new stdClass();
$config['languages']['kam']->code	= 'kam';
$config['languages']['kam']->label	= 'Kamba';

$config['languages']['kan']			= new stdClass();
$config['languages']['kan']->code	= 'kan';
$config['languages']['kan']->label	= 'Kannada';

$config['languages']['kar']			= new stdClass();
$config['languages']['kar']->code	= 'kar';
$config['languages']['kar']->label	= 'Karen languages';

$config['languages']['kas']			= new stdClass();
$config['languages']['kas']->code	= 'kas';
$config['languages']['kas']->label	= 'Kashmiri';

$config['languages']['kau']			= new stdClass();
$config['languages']['kau']->code	= 'kau';
$config['languages']['kau']->label	= 'Kanuri';

$config['languages']['kaw']			= new stdClass();
$config['languages']['kaw']->code	= 'kaw';
$config['languages']['kaw']->label	= 'Kawi';

$config['languages']['kaz']			= new stdClass();
$config['languages']['kaz']->code	= 'kaz';
$config['languages']['kaz']->label	= 'Kazakh';

$config['languages']['kbd']			= new stdClass();
$config['languages']['kbd']->code	= 'kbd';
$config['languages']['kbd']->label	= 'Kabardian';

$config['languages']['kha']			= new stdClass();
$config['languages']['kha']->code	= 'kha';
$config['languages']['kha']->label	= 'Khasi';

$config['languages']['khi']			= new stdClass();
$config['languages']['khi']->code	= 'khi';
$config['languages']['khi']->label	= 'Khoisan languages';

$config['languages']['khm']			= new stdClass();
$config['languages']['khm']->code	= 'khm';
$config['languages']['khm']->label	= 'Central Khmer';

$config['languages']['kho']			= new stdClass();
$config['languages']['kho']->code	= 'kho';
$config['languages']['kho']->label	= 'Khotanese; Sakan';

$config['languages']['kik']			= new stdClass();
$config['languages']['kik']->code	= 'kik';
$config['languages']['kik']->label	= 'Kikuyu; Gikuyu';

$config['languages']['kin']			= new stdClass();
$config['languages']['kin']->code	= 'kin';
$config['languages']['kin']->label	= 'Kinyarwanda';

$config['languages']['kir']			= new stdClass();
$config['languages']['kir']->code	= 'kir';
$config['languages']['kir']->label	= 'Kirghiz; Kyrgyz';

$config['languages']['kmb']			= new stdClass();
$config['languages']['kmb']->code	= 'kmb';
$config['languages']['kmb']->label	= 'Kimbundu';

$config['languages']['kok']			= new stdClass();
$config['languages']['kok']->code	= 'kok';
$config['languages']['kok']->label	= 'Konkani';

$config['languages']['kom']			= new stdClass();
$config['languages']['kom']->code	= 'kom';
$config['languages']['kom']->label	= 'Komi';

$config['languages']['kon']			= new stdClass();
$config['languages']['kon']->code	= 'kon';
$config['languages']['kon']->label	= 'Kongo';

$config['languages']['kor']			= new stdClass();
$config['languages']['kor']->code	= 'kor';
$config['languages']['kor']->label	= 'Korean';

$config['languages']['kos']			= new stdClass();
$config['languages']['kos']->code	= 'kos';
$config['languages']['kos']->label	= 'Kosraean';

$config['languages']['kpe']			= new stdClass();
$config['languages']['kpe']->code	= 'kpe';
$config['languages']['kpe']->label	= 'Kpelle';

$config['languages']['krc']			= new stdClass();
$config['languages']['krc']->code	= 'krc';
$config['languages']['krc']->label	= 'Karachay-Balkar';

$config['languages']['krl']			= new stdClass();
$config['languages']['krl']->code	= 'krl';
$config['languages']['krl']->label	= 'Karelian';

$config['languages']['kro']			= new stdClass();
$config['languages']['kro']->code	= 'kro';
$config['languages']['kro']->label	= 'Kru languages';

$config['languages']['kru']			= new stdClass();
$config['languages']['kru']->code	= 'kru';
$config['languages']['kru']->label	= 'Kurukh';

$config['languages']['kua']			= new stdClass();
$config['languages']['kua']->code	= 'kua';
$config['languages']['kua']->label	= 'Kuanyama; Kwanyama';

$config['languages']['kum']			= new stdClass();
$config['languages']['kum']->code	= 'kum';
$config['languages']['kum']->label	= 'Kumyk';

$config['languages']['kur']			= new stdClass();
$config['languages']['kur']->code	= 'kur';
$config['languages']['kur']->label	= 'Kurdish';

$config['languages']['kut']			= new stdClass();
$config['languages']['kut']->code	= 'kut';
$config['languages']['kut']->label	= 'Kutenai';

$config['languages']['lad']			= new stdClass();
$config['languages']['lad']->code	= 'lad';
$config['languages']['lad']->label	= 'Ladino';

$config['languages']['lah']			= new stdClass();
$config['languages']['lah']->code	= 'lah';
$config['languages']['lah']->label	= 'Lahnda';

$config['languages']['lam']			= new stdClass();
$config['languages']['lam']->code	= 'lam';
$config['languages']['lam']->label	= 'Lamba';

$config['languages']['lao']			= new stdClass();
$config['languages']['lao']->code	= 'lao';
$config['languages']['lao']->label	= 'Lao';

$config['languages']['lat']			= new stdClass();
$config['languages']['lat']->code	= 'lat';
$config['languages']['lat']->label	= 'Latin';

$config['languages']['lav']			= new stdClass();
$config['languages']['lav']->code	= 'lav';
$config['languages']['lav']->label	= 'Latvian';

$config['languages']['lez']			= new stdClass();
$config['languages']['lez']->code	= 'lez';
$config['languages']['lez']->label	= 'Lezghian';

$config['languages']['lim']			= new stdClass();
$config['languages']['lim']->code	= 'lim';
$config['languages']['lim']->label	= 'Limburgan; Limburger; Limburgish';

$config['languages']['lin']			= new stdClass();
$config['languages']['lin']->code	= 'lin';
$config['languages']['lin']->label	= 'Lingala';

$config['languages']['lit']			= new stdClass();
$config['languages']['lit']->code	= 'lit';
$config['languages']['lit']->label	= 'Lithuanian';

$config['languages']['lol']			= new stdClass();
$config['languages']['lol']->code	= 'lol';
$config['languages']['lol']->label	= 'Mongo';

$config['languages']['loz']			= new stdClass();
$config['languages']['loz']->code	= 'loz';
$config['languages']['loz']->label	= 'Lozi';

$config['languages']['ltz']			= new stdClass();
$config['languages']['ltz']->code	= 'ltz';
$config['languages']['ltz']->label	= 'Luxembourgish; Letzeburgesch';

$config['languages']['lua']			= new stdClass();
$config['languages']['lua']->code	= 'lua';
$config['languages']['lua']->label	= 'Luba-Lulua';

$config['languages']['lub']			= new stdClass();
$config['languages']['lub']->code	= 'lub';
$config['languages']['lub']->label	= 'Luba-Katanga';

$config['languages']['lug']			= new stdClass();
$config['languages']['lug']->code	= 'lug';
$config['languages']['lug']->label	= 'Ganda';

$config['languages']['lui']			= new stdClass();
$config['languages']['lui']->code	= 'lui';
$config['languages']['lui']->label	= 'Luiseno';

$config['languages']['lun']			= new stdClass();
$config['languages']['lun']->code	= 'lun';
$config['languages']['lun']->label	= 'Lunda';

$config['languages']['luo']			= new stdClass();
$config['languages']['luo']->code	= 'luo';
$config['languages']['luo']->label	= 'Luo (Kenya and Tanzania)';

$config['languages']['lus']			= new stdClass();
$config['languages']['lus']->code	= 'lus';
$config['languages']['lus']->label	= 'Lushai';

$config['languages']['mac']			= new stdClass();
$config['languages']['mac']->code	= 'mac';
$config['languages']['mac']->label	= 'Macedonian';

$config['languages']['mad']			= new stdClass();
$config['languages']['mad']->code	= 'mad';
$config['languages']['mad']->label	= 'Madurese';

$config['languages']['mag']			= new stdClass();
$config['languages']['mag']->code	= 'mag';
$config['languages']['mag']->label	= 'Magahi';

$config['languages']['mah']			= new stdClass();
$config['languages']['mah']->code	= 'mah';
$config['languages']['mah']->label	= 'Marshallese';

$config['languages']['mai']			= new stdClass();
$config['languages']['mai']->code	= 'mai';
$config['languages']['mai']->label	= 'Maithili';

$config['languages']['mak']			= new stdClass();
$config['languages']['mak']->code	= 'mak';
$config['languages']['mak']->label	= 'Makasar';

$config['languages']['mal']			= new stdClass();
$config['languages']['mal']->code	= 'mal';
$config['languages']['mal']->label	= 'Malayalam';

$config['languages']['man']			= new stdClass();
$config['languages']['man']->code	= 'man';
$config['languages']['man']->label	= 'Mandingo';

$config['languages']['mao']			= new stdClass();
$config['languages']['mao']->code	= 'mao';
$config['languages']['mao']->label	= 'Maori';

$config['languages']['map']			= new stdClass();
$config['languages']['map']->code	= 'map';
$config['languages']['map']->label	= 'Austronesian languages';

$config['languages']['mar']			= new stdClass();
$config['languages']['mar']->code	= 'mar';
$config['languages']['mar']->label	= 'Marathi';

$config['languages']['mas']			= new stdClass();
$config['languages']['mas']->code	= 'mas';
$config['languages']['mas']->label	= 'Masai';

$config['languages']['may']			= new stdClass();
$config['languages']['may']->code	= 'may';
$config['languages']['may']->label	= 'Malay';

$config['languages']['mdf']			= new stdClass();
$config['languages']['mdf']->code	= 'mdf';
$config['languages']['mdf']->label	= 'Moksha';

$config['languages']['mdr']			= new stdClass();
$config['languages']['mdr']->code	= 'mdr';
$config['languages']['mdr']->label	= 'Mandar';

$config['languages']['men']			= new stdClass();
$config['languages']['men']->code	= 'men';
$config['languages']['men']->label	= 'Mende';

$config['languages']['mic']			= new stdClass();
$config['languages']['mic']->code	= 'mic';
$config['languages']['mic']->label	= 'Mi\'kmaq; Micmac';

$config['languages']['min']			= new stdClass();
$config['languages']['min']->code	= 'min';
$config['languages']['min']->label	= 'Minangkabau';

$config['languages']['mis']			= new stdClass();
$config['languages']['mis']->code	= 'mis';
$config['languages']['mis']->label	= 'Uncoded languages';

$config['languages']['mkh']			= new stdClass();
$config['languages']['mkh']->code	= 'mkh';
$config['languages']['mkh']->label	= 'Mon-Khmer languages';

$config['languages']['mlg']			= new stdClass();
$config['languages']['mlg']->code	= 'mlg';
$config['languages']['mlg']->label	= 'Malagasy';

$config['languages']['mlt']			= new stdClass();
$config['languages']['mlt']->code	= 'mlt';
$config['languages']['mlt']->label	= 'Maltese';

$config['languages']['mnc']			= new stdClass();
$config['languages']['mnc']->code	= 'mnc';
$config['languages']['mnc']->label	= 'Manchu';

$config['languages']['mni']			= new stdClass();
$config['languages']['mni']->code	= 'mni';
$config['languages']['mni']->label	= 'Manipuri';

$config['languages']['mno']			= new stdClass();
$config['languages']['mno']->code	= 'mno';
$config['languages']['mno']->label	= 'Manobo languages';

$config['languages']['moh']			= new stdClass();
$config['languages']['moh']->code	= 'moh';
$config['languages']['moh']->label	= 'Mohawk';

$config['languages']['mon']			= new stdClass();
$config['languages']['mon']->code	= 'mon';
$config['languages']['mon']->label	= 'Mongolian';

$config['languages']['mos']			= new stdClass();
$config['languages']['mos']->code	= 'mos';
$config['languages']['mos']->label	= 'Mossi';

$config['languages']['mul']			= new stdClass();
$config['languages']['mul']->code	= 'mul';
$config['languages']['mul']->label	= 'Multiple languages';

$config['languages']['mun']			= new stdClass();
$config['languages']['mun']->code	= 'mun';
$config['languages']['mun']->label	= 'Munda languages';

$config['languages']['mus']			= new stdClass();
$config['languages']['mus']->code	= 'mus';
$config['languages']['mus']->label	= 'Creek';

$config['languages']['mwl']			= new stdClass();
$config['languages']['mwl']->code	= 'mwl';
$config['languages']['mwl']->label	= 'Mirandese';

$config['languages']['mwr']			= new stdClass();
$config['languages']['mwr']->code	= 'mwr';
$config['languages']['mwr']->label	= 'Marwari';

$config['languages']['myn']			= new stdClass();
$config['languages']['myn']->code	= 'myn';
$config['languages']['myn']->label	= 'Mayan languages';

$config['languages']['myv']			= new stdClass();
$config['languages']['myv']->code	= 'myv';
$config['languages']['myv']->label	= 'Erzya';

$config['languages']['nah']			= new stdClass();
$config['languages']['nah']->code	= 'nah';
$config['languages']['nah']->label	= 'Nahuatl languages';

$config['languages']['nai']			= new stdClass();
$config['languages']['nai']->code	= 'nai';
$config['languages']['nai']->label	= 'North American Indian languages';

$config['languages']['nap']			= new stdClass();
$config['languages']['nap']->code	= 'nap';
$config['languages']['nap']->label	= 'Neapolitan';

$config['languages']['nau']			= new stdClass();
$config['languages']['nau']->code	= 'nau';
$config['languages']['nau']->label	= 'Nauru';

$config['languages']['nav']			= new stdClass();
$config['languages']['nav']->code	= 'nav';
$config['languages']['nav']->label	= 'Navajo; Navaho';

$config['languages']['nbl']			= new stdClass();
$config['languages']['nbl']->code	= 'nbl';
$config['languages']['nbl']->label	= 'Ndebele, South; South Ndebele';

$config['languages']['nde']			= new stdClass();
$config['languages']['nde']->code	= 'nde';
$config['languages']['nde']->label	= 'Ndebele, North; North Ndebele';

$config['languages']['ndo']			= new stdClass();
$config['languages']['ndo']->code	= 'ndo';
$config['languages']['ndo']->label	= 'Ndonga';

$config['languages']['nds']			= new stdClass();
$config['languages']['nds']->code	= 'nds';
$config['languages']['nds']->label	= 'Low German; Low Saxon; German, Low; Saxon, Low';

$config['languages']['nep']			= new stdClass();
$config['languages']['nep']->code	= 'nep';
$config['languages']['nep']->label	= 'Nepali';

$config['languages']['new']			= new stdClass();
$config['languages']['new']->code	= 'new';
$config['languages']['new']->label	= 'Nepal Bhasa; Newari';

$config['languages']['nia']			= new stdClass();
$config['languages']['nia']->code	= 'nia';
$config['languages']['nia']->label	= 'Nias';

$config['languages']['nic']			= new stdClass();
$config['languages']['nic']->code	= 'nic';
$config['languages']['nic']->label	= 'Niger-Kordofanian languages';

$config['languages']['niu']			= new stdClass();
$config['languages']['niu']->code	= 'niu';
$config['languages']['niu']->label	= 'Niuean';

$config['languages']['nno']			= new stdClass();
$config['languages']['nno']->code	= 'nno';
$config['languages']['nno']->label	= 'Norwegian Nynorsk; Nynorsk, Norwegian';

$config['languages']['nob']			= new stdClass();
$config['languages']['nob']->code	= 'nob';
$config['languages']['nob']->label	= 'BokmÃ¥l, Norwegian; Norwegian BokmÃ¥l';

$config['languages']['nog']			= new stdClass();
$config['languages']['nog']->code	= 'nog';
$config['languages']['nog']->label	= 'Nogai';

$config['languages']['non']			= new stdClass();
$config['languages']['non']->code	= 'non';
$config['languages']['non']->label	= 'Norse, Old';

$config['languages']['nor']			= new stdClass();
$config['languages']['nor']->code	= 'nor';
$config['languages']['nor']->label	= 'Norwegian';

$config['languages']['nqo']			= new stdClass();
$config['languages']['nqo']->code	= 'nqo';
$config['languages']['nqo']->label	= 'N\'Ko';

$config['languages']['nso']			= new stdClass();
$config['languages']['nso']->code	= 'nso';
$config['languages']['nso']->label	= 'Pedi; Sepedi; Northern Sotho';

$config['languages']['nub']			= new stdClass();
$config['languages']['nub']->code	= 'nub';
$config['languages']['nub']->label	= 'Nubian languages';

$config['languages']['nwc']			= new stdClass();
$config['languages']['nwc']->code	= 'nwc';
$config['languages']['nwc']->label	= 'Classical Newari; Old Newari; Classical Nepal Bhasa';

$config['languages']['nya']			= new stdClass();
$config['languages']['nya']->code	= 'nya';
$config['languages']['nya']->label	= 'Chichewa; Chewa; Nyanja';

$config['languages']['nym']			= new stdClass();
$config['languages']['nym']->code	= 'nym';
$config['languages']['nym']->label	= 'Nyamwezi';

$config['languages']['nyn']			= new stdClass();
$config['languages']['nyn']->code	= 'nyn';
$config['languages']['nyn']->label	= 'Nyankole';

$config['languages']['nyo']			= new stdClass();
$config['languages']['nyo']->code	= 'nyo';
$config['languages']['nyo']->label	= 'Nyoro';

$config['languages']['nzi']			= new stdClass();
$config['languages']['nzi']->code	= 'nzi';
$config['languages']['nzi']->label	= 'Nzima';

$config['languages']['oci']			= new stdClass();
$config['languages']['oci']->code	= 'oci';
$config['languages']['oci']->label	= 'Occitan (post 1500); ProvenÃ§al';

$config['languages']['oji']			= new stdClass();
$config['languages']['oji']->code	= 'oji';
$config['languages']['oji']->label	= 'Ojibwa';

$config['languages']['ori']			= new stdClass();
$config['languages']['ori']->code	= 'ori';
$config['languages']['ori']->label	= 'Oriya';

$config['languages']['orm']			= new stdClass();
$config['languages']['orm']->code	= 'orm';
$config['languages']['orm']->label	= 'Oromo';

$config['languages']['osa']			= new stdClass();
$config['languages']['osa']->code	= 'osa';
$config['languages']['osa']->label	= 'Osage';

$config['languages']['oss']			= new stdClass();
$config['languages']['oss']->code	= 'oss';
$config['languages']['oss']->label	= 'Ossetian; Ossetic';

$config['languages']['ota']			= new stdClass();
$config['languages']['ota']->code	= 'ota';
$config['languages']['ota']->label	= 'Turkish, Ottoman (1500-1928)';

$config['languages']['oto']			= new stdClass();
$config['languages']['oto']->code	= 'oto';
$config['languages']['oto']->label	= 'Otomian languages';

$config['languages']['paa']			= new stdClass();
$config['languages']['paa']->code	= 'paa';
$config['languages']['paa']->label	= 'Papuan languages';

$config['languages']['pag']			= new stdClass();
$config['languages']['pag']->code	= 'pag';
$config['languages']['pag']->label	= 'Pangasinan';

$config['languages']['pal']			= new stdClass();
$config['languages']['pal']->code	= 'pal';
$config['languages']['pal']->label	= 'Pahlavi';

$config['languages']['pam']			= new stdClass();
$config['languages']['pam']->code	= 'pam';
$config['languages']['pam']->label	= 'Pampanga; Kapampangan';

$config['languages']['pan']			= new stdClass();
$config['languages']['pan']->code	= 'pan';
$config['languages']['pan']->label	= 'Panjabi; Punjabi';

$config['languages']['pap']			= new stdClass();
$config['languages']['pap']->code	= 'pap';
$config['languages']['pap']->label	= 'Papiamento';

$config['languages']['pau']			= new stdClass();
$config['languages']['pau']->code	= 'pau';
$config['languages']['pau']->label	= 'Palauan';

$config['languages']['per']			= new stdClass();
$config['languages']['per']->code	= 'per';
$config['languages']['per']->label	= 'Persian';

$config['languages']['phi']			= new stdClass();
$config['languages']['phi']->code	= 'phi';
$config['languages']['phi']->label	= 'Philippine languages';

$config['languages']['phn']			= new stdClass();
$config['languages']['phn']->code	= 'phn';
$config['languages']['phn']->label	= 'Phoenician';

$config['languages']['pli']			= new stdClass();
$config['languages']['pli']->code	= 'pli';
$config['languages']['pli']->label	= 'Pali';

$config['languages']['pol']			= new stdClass();
$config['languages']['pol']->code	= 'pol';
$config['languages']['pol']->label	= 'Polish';

$config['languages']['pon']			= new stdClass();
$config['languages']['pon']->code	= 'pon';
$config['languages']['pon']->label	= 'Pohnpeian';

$config['languages']['por']			= new stdClass();
$config['languages']['por']->code	= 'por';
$config['languages']['por']->label	= 'Portuguese';

$config['languages']['pra']			= new stdClass();
$config['languages']['pra']->code	= 'pra';
$config['languages']['pra']->label	= 'Prakrit languages';

$config['languages']['pus']			= new stdClass();
$config['languages']['pus']->code	= 'pus';
$config['languages']['pus']->label	= 'Pushto; Pashto';

$config['languages']['que']			= new stdClass();
$config['languages']['que']->code	= 'que';
$config['languages']['que']->label	= 'Quechua';

$config['languages']['raj']			= new stdClass();
$config['languages']['raj']->code	= 'raj';
$config['languages']['raj']->label	= 'Rajasthani';

$config['languages']['rap']			= new stdClass();
$config['languages']['rap']->code	= 'rap';
$config['languages']['rap']->label	= 'Rapanui';

$config['languages']['rar']			= new stdClass();
$config['languages']['rar']->code	= 'rar';
$config['languages']['rar']->label	= 'Rarotongan; Cook Islands Maori';

$config['languages']['roa']			= new stdClass();
$config['languages']['roa']->code	= 'roa';
$config['languages']['roa']->label	= 'Romance languages';

$config['languages']['roh']			= new stdClass();
$config['languages']['roh']->code	= 'roh';
$config['languages']['roh']->label	= 'Romansh';

$config['languages']['rom']			= new stdClass();
$config['languages']['rom']->code	= 'rom';
$config['languages']['rom']->label	= 'Romany';

$config['languages']['rum']			= new stdClass();
$config['languages']['rum']->code	= 'rum';
$config['languages']['rum']->label	= 'Romanian; Moldavian; Moldovan';

$config['languages']['run']			= new stdClass();
$config['languages']['run']->code	= 'run';
$config['languages']['run']->label	= 'Rundi';

$config['languages']['rup']			= new stdClass();
$config['languages']['rup']->code	= 'rup';
$config['languages']['rup']->label	= 'Aromanian; Arumanian; Macedo-Romanian';

$config['languages']['rus']			= new stdClass();
$config['languages']['rus']->code	= 'rus';
$config['languages']['rus']->label	= 'Russian';

$config['languages']['sad']			= new stdClass();
$config['languages']['sad']->code	= 'sad';
$config['languages']['sad']->label	= 'Sandawe';

$config['languages']['sag']			= new stdClass();
$config['languages']['sag']->code	= 'sag';
$config['languages']['sag']->label	= 'Sango';

$config['languages']['sah']			= new stdClass();
$config['languages']['sah']->code	= 'sah';
$config['languages']['sah']->label	= 'Yakut';

$config['languages']['sai']			= new stdClass();
$config['languages']['sai']->code	= 'sai';
$config['languages']['sai']->label	= 'South American Indian (Other)';

$config['languages']['sal']			= new stdClass();
$config['languages']['sal']->code	= 'sal';
$config['languages']['sal']->label	= 'Salishan languages';

$config['languages']['sam']			= new stdClass();
$config['languages']['sam']->code	= 'sam';
$config['languages']['sam']->label	= 'Samaritan Aramaic';

$config['languages']['san']			= new stdClass();
$config['languages']['san']->code	= 'san';
$config['languages']['san']->label	= 'Sanskrit';

$config['languages']['sas']			= new stdClass();
$config['languages']['sas']->code	= 'sas';
$config['languages']['sas']->label	= 'Sasak';

$config['languages']['sat']			= new stdClass();
$config['languages']['sat']->code	= 'sat';
$config['languages']['sat']->label	= 'Santali';

$config['languages']['scn']			= new stdClass();
$config['languages']['scn']->code	= 'scn';
$config['languages']['scn']->label	= 'Sicilian';

$config['languages']['sco']			= new stdClass();
$config['languages']['sco']->code	= 'sco';
$config['languages']['sco']->label	= 'Scots';

$config['languages']['sel']			= new stdClass();
$config['languages']['sel']->code	= 'sel';
$config['languages']['sel']->label	= 'Selkup';

$config['languages']['sem']			= new stdClass();
$config['languages']['sem']->code	= 'sem';
$config['languages']['sem']->label	= 'Semitic languages';

$config['languages']['sgn']			= new stdClass();
$config['languages']['sgn']->code	= 'sgn';
$config['languages']['sgn']->label	= 'Sign Languages';

$config['languages']['shn']			= new stdClass();
$config['languages']['shn']->code	= 'shn';
$config['languages']['shn']->label	= 'Shan';

$config['languages']['sid']			= new stdClass();
$config['languages']['sid']->code	= 'sid';
$config['languages']['sid']->label	= 'Sidamo';

$config['languages']['sin']			= new stdClass();
$config['languages']['sin']->code	= 'sin';
$config['languages']['sin']->label	= 'Sinhala; Sinhalese';

$config['languages']['sio']			= new stdClass();
$config['languages']['sio']->code	= 'sio';
$config['languages']['sio']->label	= 'Siouan languages';

$config['languages']['sit']			= new stdClass();
$config['languages']['sit']->code	= 'sit';
$config['languages']['sit']->label	= 'Sino-Tibetan languages';

$config['languages']['sla']			= new stdClass();
$config['languages']['sla']->code	= 'sla';
$config['languages']['sla']->label	= 'Slavic languages';

$config['languages']['slo']			= new stdClass();
$config['languages']['slo']->code	= 'slo';
$config['languages']['slo']->label	= 'Slovak';

$config['languages']['slv']			= new stdClass();
$config['languages']['slv']->code	= 'slv';
$config['languages']['slv']->label	= 'Slovenian';

$config['languages']['sma']			= new stdClass();
$config['languages']['sma']->code	= 'sma';
$config['languages']['sma']->label	= 'Southern Sami';

$config['languages']['sme']			= new stdClass();
$config['languages']['sme']->code	= 'sme';
$config['languages']['sme']->label	= 'Northern Sami';

$config['languages']['smi']			= new stdClass();
$config['languages']['smi']->code	= 'smi';
$config['languages']['smi']->label	= 'Sami languages';

$config['languages']['smj']			= new stdClass();
$config['languages']['smj']->code	= 'smj';
$config['languages']['smj']->label	= 'Lule Sami';

$config['languages']['smn']			= new stdClass();
$config['languages']['smn']->code	= 'smn';
$config['languages']['smn']->label	= 'Inari Sami';

$config['languages']['smo']			= new stdClass();
$config['languages']['smo']->code	= 'smo';
$config['languages']['smo']->label	= 'Samoan';

$config['languages']['sms']			= new stdClass();
$config['languages']['sms']->code	= 'sms';
$config['languages']['sms']->label	= 'Skolt Sami';

$config['languages']['sna']			= new stdClass();
$config['languages']['sna']->code	= 'sna';
$config['languages']['sna']->label	= 'Shona';

$config['languages']['snd']			= new stdClass();
$config['languages']['snd']->code	= 'snd';
$config['languages']['snd']->label	= 'Sindhi';

$config['languages']['snk']			= new stdClass();
$config['languages']['snk']->code	= 'snk';
$config['languages']['snk']->label	= 'Soninke';

$config['languages']['sog']			= new stdClass();
$config['languages']['sog']->code	= 'sog';
$config['languages']['sog']->label	= 'Sogdian';

$config['languages']['som']			= new stdClass();
$config['languages']['som']->code	= 'som';
$config['languages']['som']->label	= 'Somali';

$config['languages']['son']			= new stdClass();
$config['languages']['son']->code	= 'son';
$config['languages']['son']->label	= 'Songhai languages';

$config['languages']['sot']			= new stdClass();
$config['languages']['sot']->code	= 'sot';
$config['languages']['sot']->label	= 'Sotho, Southern';

$config['languages']['srd']			= new stdClass();
$config['languages']['srd']->code	= 'srd';
$config['languages']['srd']->label	= 'Sardinian';

$config['languages']['srn']			= new stdClass();
$config['languages']['srn']->code	= 'srn';
$config['languages']['srn']->label	= 'Sranan Tongo';

$config['languages']['srp']			= new stdClass();
$config['languages']['srp']->code	= 'srp';
$config['languages']['srp']->label	= 'Serbian';

$config['languages']['srr']			= new stdClass();
$config['languages']['srr']->code	= 'srr';
$config['languages']['srr']->label	= 'Serer';

$config['languages']['ssa']			= new stdClass();
$config['languages']['ssa']->code	= 'ssa';
$config['languages']['ssa']->label	= 'Nilo-Saharan languages';

$config['languages']['ssw']			= new stdClass();
$config['languages']['ssw']->code	= 'ssw';
$config['languages']['ssw']->label	= 'Swati';

$config['languages']['suk']			= new stdClass();
$config['languages']['suk']->code	= 'suk';
$config['languages']['suk']->label	= 'Sukuma';

$config['languages']['sun']			= new stdClass();
$config['languages']['sun']->code	= 'sun';
$config['languages']['sun']->label	= 'Sundanese';

$config['languages']['sus']			= new stdClass();
$config['languages']['sus']->code	= 'sus';
$config['languages']['sus']->label	= 'Susu';

$config['languages']['sux']			= new stdClass();
$config['languages']['sux']->code	= 'sux';
$config['languages']['sux']->label	= 'Sumerian';

$config['languages']['swa']			= new stdClass();
$config['languages']['swa']->code	= 'swa';
$config['languages']['swa']->label	= 'Swahili';

$config['languages']['swe']			= new stdClass();
$config['languages']['swe']->code	= 'swe';
$config['languages']['swe']->label	= 'Swedish';

$config['languages']['syc']			= new stdClass();
$config['languages']['syc']->code	= 'syc';
$config['languages']['syc']->label	= 'Classical Syriac';

$config['languages']['syr']			= new stdClass();
$config['languages']['syr']->code	= 'syr';
$config['languages']['syr']->label	= 'Syriac';

$config['languages']['tah']			= new stdClass();
$config['languages']['tah']->code	= 'tah';
$config['languages']['tah']->label	= 'Tahitian';

$config['languages']['tai']			= new stdClass();
$config['languages']['tai']->code	= 'tai';
$config['languages']['tai']->label	= 'Tai languages';

$config['languages']['tam']			= new stdClass();
$config['languages']['tam']->code	= 'tam';
$config['languages']['tam']->label	= 'Tamil';

$config['languages']['tat']			= new stdClass();
$config['languages']['tat']->code	= 'tat';
$config['languages']['tat']->label	= 'Tatar';

$config['languages']['tel']			= new stdClass();
$config['languages']['tel']->code	= 'tel';
$config['languages']['tel']->label	= 'Telugu';

$config['languages']['tem']			= new stdClass();
$config['languages']['tem']->code	= 'tem';
$config['languages']['tem']->label	= 'Timne';

$config['languages']['ter']			= new stdClass();
$config['languages']['ter']->code	= 'ter';
$config['languages']['ter']->label	= 'Tereno';

$config['languages']['tet']			= new stdClass();
$config['languages']['tet']->code	= 'tet';
$config['languages']['tet']->label	= 'Tetum';

$config['languages']['tgk']			= new stdClass();
$config['languages']['tgk']->code	= 'tgk';
$config['languages']['tgk']->label	= 'Tajik';

$config['languages']['tgl']			= new stdClass();
$config['languages']['tgl']->code	= 'tgl';
$config['languages']['tgl']->label	= 'Tagalog';

$config['languages']['tha']			= new stdClass();
$config['languages']['tha']->code	= 'tha';
$config['languages']['tha']->label	= 'Thai';

$config['languages']['tib']			= new stdClass();
$config['languages']['tib']->code	= 'tib';
$config['languages']['tib']->label	= 'Tibetan';

$config['languages']['tig']			= new stdClass();
$config['languages']['tig']->code	= 'tig';
$config['languages']['tig']->label	= 'Tigre';

$config['languages']['tir']			= new stdClass();
$config['languages']['tir']->code	= 'tir';
$config['languages']['tir']->label	= 'Tigrinya';

$config['languages']['tiv']			= new stdClass();
$config['languages']['tiv']->code	= 'tiv';
$config['languages']['tiv']->label	= 'Tiv';

$config['languages']['tkl']			= new stdClass();
$config['languages']['tkl']->code	= 'tkl';
$config['languages']['tkl']->label	= 'Tokelau';

$config['languages']['tlh']			= new stdClass();
$config['languages']['tlh']->code	= 'tlh';
$config['languages']['tlh']->label	= 'Klingon; tlhIngan-Hol';

$config['languages']['tli']			= new stdClass();
$config['languages']['tli']->code	= 'tli';
$config['languages']['tli']->label	= 'Tlingit';

$config['languages']['tmh']			= new stdClass();
$config['languages']['tmh']->code	= 'tmh';
$config['languages']['tmh']->label	= 'Tamashek';

$config['languages']['tog']			= new stdClass();
$config['languages']['tog']->code	= 'tog';
$config['languages']['tog']->label	= 'Tonga (Nyasa)';

$config['languages']['ton']			= new stdClass();
$config['languages']['ton']->code	= 'ton';
$config['languages']['ton']->label	= 'Tonga (Tonga Islands)';

$config['languages']['tpi']			= new stdClass();
$config['languages']['tpi']->code	= 'tpi';
$config['languages']['tpi']->label	= 'Tok Pisin';

$config['languages']['tsi']			= new stdClass();
$config['languages']['tsi']->code	= 'tsi';
$config['languages']['tsi']->label	= 'Tsimshian';

$config['languages']['tsn']			= new stdClass();
$config['languages']['tsn']->code	= 'tsn';
$config['languages']['tsn']->label	= 'Tswana';

$config['languages']['tso']			= new stdClass();
$config['languages']['tso']->code	= 'tso';
$config['languages']['tso']->label	= 'Tsonga';

$config['languages']['tuk']			= new stdClass();
$config['languages']['tuk']->code	= 'tuk';
$config['languages']['tuk']->label	= 'Turkmen';

$config['languages']['tum']			= new stdClass();
$config['languages']['tum']->code	= 'tum';
$config['languages']['tum']->label	= 'Tumbuka';

$config['languages']['tup']			= new stdClass();
$config['languages']['tup']->code	= 'tup';
$config['languages']['tup']->label	= 'Tupi languages';

$config['languages']['tur']			= new stdClass();
$config['languages']['tur']->code	= 'tur';
$config['languages']['tur']->label	= 'Turkish';

$config['languages']['tut']			= new stdClass();
$config['languages']['tut']->code	= 'tut';
$config['languages']['tut']->label	= 'Altaic languages';

$config['languages']['tvl']			= new stdClass();
$config['languages']['tvl']->code	= 'tvl';
$config['languages']['tvl']->label	= 'Tuvalu';

$config['languages']['twi']			= new stdClass();
$config['languages']['twi']->code	= 'twi';
$config['languages']['twi']->label	= 'Twi';

$config['languages']['tyv']			= new stdClass();
$config['languages']['tyv']->code	= 'tyv';
$config['languages']['tyv']->label	= 'Tuvinian';

$config['languages']['udm']			= new stdClass();
$config['languages']['udm']->code	= 'udm';
$config['languages']['udm']->label	= 'Udmurt';

$config['languages']['uga']			= new stdClass();
$config['languages']['uga']->code	= 'uga';
$config['languages']['uga']->label	= 'Ugaritic';

$config['languages']['uig']			= new stdClass();
$config['languages']['uig']->code	= 'uig';
$config['languages']['uig']->label	= 'Uighur; Uyghur';

$config['languages']['ukr']			= new stdClass();
$config['languages']['ukr']->code	= 'ukr';
$config['languages']['ukr']->label	= 'Ukrainian';

$config['languages']['umb']			= new stdClass();
$config['languages']['umb']->code	= 'umb';
$config['languages']['umb']->label	= 'Umbundu';

$config['languages']['und']			= new stdClass();
$config['languages']['und']->code	= 'und';
$config['languages']['und']->label	= 'Undetermined';

$config['languages']['urd']			= new stdClass();
$config['languages']['urd']->code	= 'urd';
$config['languages']['urd']->label	= 'Urdu';

$config['languages']['uzb']			= new stdClass();
$config['languages']['uzb']->code	= 'uzb';
$config['languages']['uzb']->label	= 'Uzbek';

$config['languages']['vai']			= new stdClass();
$config['languages']['vai']->code	= 'vai';
$config['languages']['vai']->label	= 'Vai';

$config['languages']['ven']			= new stdClass();
$config['languages']['ven']->code	= 'ven';
$config['languages']['ven']->label	= 'Venda';

$config['languages']['vie']			= new stdClass();
$config['languages']['vie']->code	= 'vie';
$config['languages']['vie']->label	= 'Vietnamese';

$config['languages']['vol']			= new stdClass();
$config['languages']['vol']->code	= 'vol';
$config['languages']['vol']->label	= 'VolapÃ¼k';

$config['languages']['vot']			= new stdClass();
$config['languages']['vot']->code	= 'vot';
$config['languages']['vot']->label	= 'Votic';

$config['languages']['wak']			= new stdClass();
$config['languages']['wak']->code	= 'wak';
$config['languages']['wak']->label	= 'Wakashan languages';

$config['languages']['wal']			= new stdClass();
$config['languages']['wal']->code	= 'wal';
$config['languages']['wal']->label	= 'Walamo';

$config['languages']['war']			= new stdClass();
$config['languages']['war']->code	= 'war';
$config['languages']['war']->label	= 'Waray';

$config['languages']['was']			= new stdClass();
$config['languages']['was']->code	= 'was';
$config['languages']['was']->label	= 'Washo';

$config['languages']['wel']			= new stdClass();
$config['languages']['wel']->code	= 'wel';
$config['languages']['wel']->label	= 'Welsh';

$config['languages']['wen']			= new stdClass();
$config['languages']['wen']->code	= 'wen';
$config['languages']['wen']->label	= 'Sorbian languages';

$config['languages']['wln']			= new stdClass();
$config['languages']['wln']->code	= 'wln';
$config['languages']['wln']->label	= 'Walloon';

$config['languages']['wol']			= new stdClass();
$config['languages']['wol']->code	= 'wol';
$config['languages']['wol']->label	= 'Wolof';

$config['languages']['xal']			= new stdClass();
$config['languages']['xal']->code	= 'xal';
$config['languages']['xal']->label	= 'Kalmyk; Oirat';

$config['languages']['xho']			= new stdClass();
$config['languages']['xho']->code	= 'xho';
$config['languages']['xho']->label	= 'Xhosa';

$config['languages']['yao']			= new stdClass();
$config['languages']['yao']->code	= 'yao';
$config['languages']['yao']->label	= 'Yao';

$config['languages']['yap']			= new stdClass();
$config['languages']['yap']->code	= 'yap';
$config['languages']['yap']->label	= 'Yapese';

$config['languages']['yid']			= new stdClass();
$config['languages']['yid']->code	= 'yid';
$config['languages']['yid']->label	= 'Yiddish';

$config['languages']['yor']			= new stdClass();
$config['languages']['yor']->code	= 'yor';
$config['languages']['yor']->label	= 'Yoruba';

$config['languages']['ypk']			= new stdClass();
$config['languages']['ypk']->code	= 'ypk';
$config['languages']['ypk']->label	= 'Yupik languages';

$config['languages']['zap']			= new stdClass();
$config['languages']['zap']->code	= 'zap';
$config['languages']['zap']->label	= 'Zapotec';

$config['languages']['zbl']			= new stdClass();
$config['languages']['zbl']->code	= 'zbl';
$config['languages']['zbl']->label	= 'Blissymbols; Blissymbolics; Bliss';

$config['languages']['zen']			= new stdClass();
$config['languages']['zen']->code	= 'zen';
$config['languages']['zen']->label	= 'Zenaga';

$config['languages']['zgh']			= new stdClass();
$config['languages']['zgh']->code	= 'zgh';
$config['languages']['zgh']->label	= 'Standard Moroccan Tamazight';

$config['languages']['zha']			= new stdClass();
$config['languages']['zha']->code	= 'zha';
$config['languages']['zha']->label	= 'Zhuang; Chuang';

$config['languages']['znd']			= new stdClass();
$config['languages']['znd']->code	= 'znd';
$config['languages']['znd']->label	= 'Zande languages';

$config['languages']['zul']			= new stdClass();
$config['languages']['zul']->code	= 'zul';
$config['languages']['zul']->label	= 'Zulu';

$config['languages']['zun']			= new stdClass();
$config['languages']['zun']->code	= 'zun';
$config['languages']['zun']->label	= 'Zuni';

$config['languages']['zza']			= new stdClass();
$config['languages']['zza']->code	= 'zza';
$config['languages']['zza']->label	= 'Zaza; Dimili; Dimli; Kirdki; Kirmanjki; Zazaki';

/* End of file languages.php */
/* Location: ./config/languages.php */