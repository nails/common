<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| CONTINENTS
|--------------------------------------------------------------------------
|
| The following is an ISO list of continents
|
*/

$config['continents'] = array();

$config['continents']['AF']	= 'Africa';
$config['continents']['AN']	= 'Antarctica';
$config['continents']['AS']	= 'Asia';
$config['continents']['OC']	= 'Australia (Oceania)';
$config['continents']['EU']	= 'Europe';
$config['continents']['NA']	= 'North America';
$config['continents']['SA']	= 'South America';


// --------------------------------------------------------------------------


/*
|--------------------------------------------------------------------------
| COUNTRIES
|--------------------------------------------------------------------------
|
| This config file contains an ISO list of countries.
|
*/

$config['countries'] = array();

//	Special treatment :>
$config['countries']['GB']				= new stdClass();
$config['countries']['GB']->code		= 'GB';
$config['countries']['GB']->code_3		= 'GBR';
$config['countries']['GB']->number		= '826';
$config['countries']['GB']->label		= 'United Kingdom';
$config['countries']['GB']->continent	= 'EU';
$config['countries']['GB']->currency	= 'GBP';

$config['countries']['US']				= new stdClass();
$config['countries']['US']->code		= 'US';
$config['countries']['US']->code_3		= 'USA';
$config['countries']['US']->number		= '840';
$config['countries']['US']->label		= 'United States';
$config['countries']['US']->continent	= 'NA';
$config['countries']['US']->currency	= 'USD';

// --------------------------------------------------------------------------

$config['countries']['AF']				= new stdClass();
$config['countries']['AF']->code		= 'AF';
$config['countries']['AF']->code_3		= 'AFG';
$config['countries']['AF']->number		= '004';
$config['countries']['AF']->label		= 'Afghanistan';
$config['countries']['AF']->continent	= 'AS';
$config['countries']['AF']->currency	= 'AFN';

$config['countries']['AX']				= new stdClass();
$config['countries']['AX']->code		= 'AX';
$config['countries']['AX']->code_3		= 'ALA';
$config['countries']['AX']->number		= '248';
$config['countries']['AX']->label		= 'Åland Islands';
$config['countries']['AX']->continent	= 'EU';
$config['countries']['AX']->currency	= 'FKP';

$config['countries']['AL']				= new stdClass();
$config['countries']['AL']->code		= 'AL';
$config['countries']['AL']->code_3		= 'ALB';
$config['countries']['AL']->number		= '008';
$config['countries']['AL']->label		= 'Albania';
$config['countries']['AL']->continent	= 'EU';
$config['countries']['AL']->currency	= 'ALL';

$config['countries']['DZ']				= new stdClass();
$config['countries']['DZ']->code		= 'DZ';
$config['countries']['DZ']->code_3		= 'DZA';
$config['countries']['DZ']->number		= '012';
$config['countries']['DZ']->label		= 'Algeria';
$config['countries']['DZ']->continent	= 'AF';
$config['countries']['DZ']->currency	= 'DZD';

$config['countries']['AS']				= new stdClass();
$config['countries']['AS']->code		= 'AS';
$config['countries']['AS']->code_3		= 'ASM';
$config['countries']['AS']->number		= '016';
$config['countries']['AS']->label		= 'American Samoa';
$config['countries']['AS']->continent	= 'OC';
$config['countries']['AS']->currency	= 'USD';

$config['countries']['AD']				= new stdClass();
$config['countries']['AD']->code		= 'AD';
$config['countries']['AD']->code_3		= 'AND';
$config['countries']['AD']->number		= '020';
$config['countries']['AD']->label		= 'Andorra';
$config['countries']['AD']->continent	= 'EU';
$config['countries']['AD']->currency	= 'EUR';

$config['countries']['AO']				= new stdClass();
$config['countries']['AO']->code		= 'AO';
$config['countries']['AO']->code_3		= 'AGO';
$config['countries']['AO']->number		= '024';
$config['countries']['AO']->label		= 'Angola';
$config['countries']['AO']->continent	= 'AF';
$config['countries']['AO']->currency	= 'AOA';

$config['countries']['AI']				= new stdClass();
$config['countries']['AI']->code		= 'AI';
$config['countries']['AI']->code_3		= 'AIA';
$config['countries']['AI']->number		= '660';
$config['countries']['AI']->label		= 'Anguilla';
$config['countries']['AI']->continent	= 'NA';
$config['countries']['AI']->currency	= 'XCD';

$config['countries']['AG']				= new stdClass();
$config['countries']['AG']->code		= 'AG';
$config['countries']['AG']->code_3		= 'ATG';
$config['countries']['AG']->number		= '028';
$config['countries']['AG']->label		= 'Antigua and Barbuda';
$config['countries']['AG']->continent	= 'NA';
$config['countries']['AG']->currency	= 'XCD';

$config['countries']['AR']				= new stdClass();
$config['countries']['AR']->code		= 'AR';
$config['countries']['AR']->code_3		= 'ARG';
$config['countries']['AR']->number		= '032';
$config['countries']['AR']->label		= 'Argentina';
$config['countries']['AR']->continent	= 'SA';
$config['countries']['AR']->currency	= 'ARS';

$config['countries']['AM']				= new stdClass();
$config['countries']['AM']->code		= 'AM';
$config['countries']['AM']->code_3		= 'ARM';
$config['countries']['AM']->number		= '051';
$config['countries']['AM']->label		= 'Armenia';
$config['countries']['AM']->continent	= 'AS';
$config['countries']['AM']->currency	= 'AMD';

$config['countries']['AW']				= new stdClass();
$config['countries']['AW']->code		= 'AW';
$config['countries']['AW']->code_3		= 'ABW';
$config['countries']['AW']->number		= '533';
$config['countries']['AW']->label		= 'Aruba';
$config['countries']['AW']->continent	= 'NA';
$config['countries']['AW']->currency	= 'AWG';

$config['countries']['AU']				= new stdClass();
$config['countries']['AU']->code		= 'AU';
$config['countries']['AU']->code_3		= 'AUS';
$config['countries']['AU']->number		= '036';
$config['countries']['AU']->label		= 'Australia';
$config['countries']['AU']->continent	= 'OC';
$config['countries']['AU']->currency	= 'AUD';

$config['countries']['AT']				= new stdClass();
$config['countries']['AT']->code		= 'AT';
$config['countries']['AT']->code_3		= 'AUT';
$config['countries']['AT']->number		= '040';
$config['countries']['AT']->label		= 'Austria';
$config['countries']['AT']->continent	= 'EU';
$config['countries']['AT']->currency	= 'EUR';

$config['countries']['AZ']				= new stdClass();
$config['countries']['AZ']->code		= 'AZ';
$config['countries']['AZ']->code_3		= 'AZE';
$config['countries']['AZ']->number		= '031';
$config['countries']['AZ']->label		= 'Azerbaijan';
$config['countries']['AZ']->continent	= 'AS';
$config['countries']['AZ']->currency	= 'AZN';

$config['countries']['BS']				= new stdClass();
$config['countries']['BS']->code		= 'BS';
$config['countries']['BS']->code_3		= 'BHS';
$config['countries']['BS']->number		= '044';
$config['countries']['BS']->label		= 'Bahamas';
$config['countries']['BS']->continent	= 'NA';
$config['countries']['BS']->currency	= 'BSD';

$config['countries']['BH']				= new stdClass();
$config['countries']['BH']->code		= 'BH';
$config['countries']['BH']->code_3		= 'BHR';
$config['countries']['BH']->number		= '048';
$config['countries']['BH']->label		= 'Bahrain';
$config['countries']['BH']->continent	= 'AS';
$config['countries']['BH']->currency	= 'BHD';

$config['countries']['BD']				= new stdClass();
$config['countries']['BD']->code		= 'BD';
$config['countries']['BD']->code_3		= 'BGD';
$config['countries']['BD']->number		= '050';
$config['countries']['BD']->label		= 'Bangladesh';
$config['countries']['BD']->continent	= 'AS';
$config['countries']['BD']->currency	= 'BDT';

$config['countries']['BB']				= new stdClass();
$config['countries']['BB']->code		= 'BB';
$config['countries']['BB']->code_3		= 'BRB';
$config['countries']['BB']->number		= '052';
$config['countries']['BB']->label		= 'Barbados';
$config['countries']['BB']->continent	= 'NA';
$config['countries']['BB']->currency	= 'BBD';

$config['countries']['BY']				= new stdClass();
$config['countries']['BY']->code		= 'BY';
$config['countries']['BY']->code_3		= 'BLR';
$config['countries']['BY']->number		= '112';
$config['countries']['BY']->label		= 'Belarus';
$config['countries']['BY']->continent	= 'EU';
$config['countries']['BY']->currency	= 'BYR';

$config['countries']['BE']				= new stdClass();
$config['countries']['BE']->code		= 'BE';
$config['countries']['BE']->code_3		= 'BEL';
$config['countries']['BE']->number		= '056';
$config['countries']['BE']->label		= 'Belgium';
$config['countries']['BE']->continent	= 'EU';
$config['countries']['BE']->currency	= 'EUR';

$config['countries']['BZ']				= new stdClass();
$config['countries']['BZ']->code		= 'BZ';
$config['countries']['BZ']->code_3		= 'BLZ';
$config['countries']['BZ']->number		= '084';
$config['countries']['BZ']->label		= 'Belize';
$config['countries']['BZ']->continent	= 'NA';
$config['countries']['BZ']->currency	= 'BZD';

$config['countries']['BJ']				= new stdClass();
$config['countries']['BJ']->code		= 'BJ';
$config['countries']['BJ']->code_3		= 'BEN';
$config['countries']['BJ']->number		= '204';
$config['countries']['BJ']->label		= 'Benin';
$config['countries']['BJ']->continent	= 'AF';
$config['countries']['BJ']->currency	= 'XAF';

$config['countries']['BM']				= new stdClass();
$config['countries']['BM']->code		= 'BM';
$config['countries']['BM']->code_3		= 'BMU';
$config['countries']['BM']->number		= '060';
$config['countries']['BM']->label		= 'Bermuda';
$config['countries']['BM']->continent	= 'NA';
$config['countries']['BM']->currency	= 'BMD';

$config['countries']['BT']				= new stdClass();
$config['countries']['BT']->code		= 'BT';
$config['countries']['BT']->code_3		= 'BTN';
$config['countries']['BT']->number		= '064';
$config['countries']['BT']->label		= 'Bhutan';
$config['countries']['BT']->continent	= 'AS';
$config['countries']['BT']->currency	= 'BTN';

$config['countries']['BO']				= new stdClass();
$config['countries']['BO']->code		= 'BO';
$config['countries']['BO']->code_3		= 'BOL';
$config['countries']['BO']->number		= '068';
$config['countries']['BO']->label		= 'Bolivia';
$config['countries']['BO']->continent	= 'SA';
$config['countries']['BO']->currency	= 'BOB';

$config['countries']['BA']				= new stdClass();
$config['countries']['BA']->code		= 'BA';
$config['countries']['BA']->code_3		= 'BIH';
$config['countries']['BA']->number		= '070';
$config['countries']['BA']->label		= 'Bosnia and Herzegovina';
$config['countries']['BA']->continent	= 'EU';
$config['countries']['BA']->currency	= 'BAM';

$config['countries']['BW']				= new stdClass();
$config['countries']['BW']->code		= 'BW';
$config['countries']['BW']->code_3		= 'BWA';
$config['countries']['BW']->number		= '072';
$config['countries']['BW']->label		= 'Botswana';
$config['countries']['BW']->continent	= 'AF';
$config['countries']['BW']->currency	= 'BWP';

$config['countries']['BR']				= new stdClass();
$config['countries']['BR']->code		= 'BR';
$config['countries']['BR']->code_3		= 'BRA';
$config['countries']['BR']->number		= '076';
$config['countries']['BR']->label		= 'Brazil';
$config['countries']['BR']->continent	= 'SA';
$config['countries']['BR']->currency	= 'BRL';

$config['countries']['IO']				= new stdClass();
$config['countries']['IO']->code		= 'IO';
$config['countries']['IO']->code_3		= 'IOT';
$config['countries']['IO']->number		= '086';
$config['countries']['IO']->label		= 'British Indian Ocean Territory';
$config['countries']['IO']->continent	= 'AS';
$config['countries']['IO']->currency	= 'GBP';

$config['countries']['BN']				= new stdClass();
$config['countries']['BN']->code		= 'BN';
$config['countries']['BN']->code_3		= 'BRN';
$config['countries']['BN']->number		= '096';
$config['countries']['BN']->label		= 'Brunei Darussalam';
$config['countries']['BN']->continent	= 'AS';
$config['countries']['BN']->currency	= 'BND';

$config['countries']['BG']				= new stdClass();
$config['countries']['BG']->code		= 'BG';
$config['countries']['BG']->code_3		= 'BGR';
$config['countries']['BG']->number		= '100';
$config['countries']['BG']->label		= 'Bulgaria';
$config['countries']['BG']->continent	= 'EU';
$config['countries']['BG']->currency	= 'BGN';

$config['countries']['BF']				= new stdClass();
$config['countries']['BF']->code		= 'BF';
$config['countries']['BF']->code_3		= 'BFA';
$config['countries']['BF']->number		= '854';
$config['countries']['BF']->label		= 'Burkina Faso';
$config['countries']['BF']->continent	= 'AF';
$config['countries']['BF']->currency	= 'XAF';

$config['countries']['BI']				= new stdClass();
$config['countries']['BI']->code		= 'BI';
$config['countries']['BI']->code_3		= 'BDI';
$config['countries']['BI']->number		= '108';
$config['countries']['BI']->label		= 'Burundi';
$config['countries']['BI']->continent	= 'AF';
$config['countries']['BI']->currency	= 'BIF';

$config['countries']['KH']				= new stdClass();
$config['countries']['KH']->code		= 'KH';
$config['countries']['KH']->code_3		= 'KHM';
$config['countries']['KH']->number		= '116';
$config['countries']['KH']->label		= 'Cambodia';
$config['countries']['KH']->continent	= 'AS';
$config['countries']['KH']->currency	= 'KHR';

$config['countries']['CM']				= new stdClass();
$config['countries']['CM']->code		= 'CM';
$config['countries']['CM']->code_3		= 'CMR';
$config['countries']['CM']->number		= '120';
$config['countries']['CM']->label		= 'Cameroon';
$config['countries']['CM']->continent	= 'AF';
$config['countries']['CM']->currency	= 'XAF';

$config['countries']['CA']				= new stdClass();
$config['countries']['CA']->code		= 'CA';
$config['countries']['CA']->code_3		= 'CAN';
$config['countries']['CA']->number		= '124';
$config['countries']['CA']->label		= 'Canada';
$config['countries']['CA']->continent	= 'NA';
$config['countries']['CA']->currency	= 'CAD';

$config['countries']['CV']				= new stdClass();
$config['countries']['CV']->code		= 'CV';
$config['countries']['CV']->code_3		= 'CPV';
$config['countries']['CV']->number		= '132';
$config['countries']['CV']->label		= 'Cape Verde';
$config['countries']['CV']->continent	= 'AF';
$config['countries']['CV']->currency	= 'CVE';

$config['countries']['KY']				= new stdClass();
$config['countries']['KY']->code		= 'KY';
$config['countries']['KY']->code_3		= 'CYM';
$config['countries']['KY']->number		= '136';
$config['countries']['KY']->label		= 'Cayman Islands';
$config['countries']['KY']->continent	= 'NA';
$config['countries']['KY']->currency	= 'KYD';

$config['countries']['CF']				= new stdClass();
$config['countries']['CF']->code		= 'CF';
$config['countries']['CF']->code_3		= 'CAF';
$config['countries']['CF']->number		= '140';
$config['countries']['CF']->label		= 'Central African Republic';
$config['countries']['CF']->continent	= 'AF';
$config['countries']['CF']->currency	= 'XAF';

$config['countries']['TD']				= new stdClass();
$config['countries']['TD']->code		= 'TD';
$config['countries']['TD']->code_3		= 'TCD';
$config['countries']['TD']->number		= '148';
$config['countries']['TD']->label		= 'Chad';
$config['countries']['TD']->continent	= 'AF';
$config['countries']['TD']->currency	= 'XAF';

$config['countries']['CL']				= new stdClass();
$config['countries']['CL']->code		= 'CL';
$config['countries']['CL']->code_3		= 'CHL';
$config['countries']['CL']->number		= '152';
$config['countries']['CL']->label		= 'Chile';
$config['countries']['CL']->continent	= 'SA';
$config['countries']['CL']->currency	= 'CLP';

$config['countries']['CN']				= new stdClass();
$config['countries']['CN']->code		= 'CN';
$config['countries']['CN']->code_3		= 'CHN';
$config['countries']['CN']->number		= '156';
$config['countries']['CN']->label		= 'China';
$config['countries']['CN']->continent	= 'AS';
$config['countries']['CN']->currency	= 'CNY';

$config['countries']['CX']				= new stdClass();
$config['countries']['CX']->code		= 'CX';
$config['countries']['CX']->code_3		= 'CXR';
$config['countries']['CX']->number		= '162';
$config['countries']['CX']->label		= 'Christmas Island';
$config['countries']['CX']->continent	= 'AS';
$config['countries']['CX']->currency	= 'AUD';

$config['countries']['CC']				= new stdClass();
$config['countries']['CC']->code		= 'CC';
$config['countries']['CC']->code_3		= 'CCK';
$config['countries']['CC']->number		= '166';
$config['countries']['CC']->label		= 'Cocos (Keeling) Islands';
$config['countries']['CC']->continent	= 'AS';
$config['countries']['CC']->currency	= 'AUD';

$config['countries']['CO']				= new stdClass();
$config['countries']['CO']->code		= 'CO';
$config['countries']['CO']->code_3		= 'COL';
$config['countries']['CO']->number		= '170';
$config['countries']['CO']->label		= 'Colombia';
$config['countries']['CO']->continent	= 'SA';
$config['countries']['CO']->currency	= 'COP';

$config['countries']['KM']				= new stdClass();
$config['countries']['KM']->code		= 'KM';
$config['countries']['KM']->code_3		= 'COM';
$config['countries']['KM']->number		= '174';
$config['countries']['KM']->label		= 'Comoros';
$config['countries']['KM']->continent	= 'AF';
$config['countries']['KM']->currency	= 'KMF';

$config['countries']['CD']				= new stdClass();
$config['countries']['CD']->code		= 'CD';
$config['countries']['CD']->code_3		= 'COD';
$config['countries']['CD']->number		= '180';
$config['countries']['CD']->label		= 'Congo, Democratic Republic of (was Zaire)';
$config['countries']['CD']->continent	= 'AF';
$config['countries']['CD']->currency	= 'CDF';

$config['countries']['CG']				= new stdClass();
$config['countries']['CG']->code		= 'CG';
$config['countries']['CG']->code_3		= 'COG';
$config['countries']['CG']->number		= '178';
$config['countries']['CG']->label		= 'Congo, Republic of';
$config['countries']['CG']->continent	= 'AF';
$config['countries']['CG']->currency	= 'CDF';

$config['countries']['CK']				= new stdClass();
$config['countries']['CK']->code		= 'CK';
$config['countries']['CK']->code_3		= 'COK';
$config['countries']['CK']->number		= '184';
$config['countries']['CK']->label		= 'Cook Islands';
$config['countries']['CK']->continent	= 'OC';
$config['countries']['CK']->currency	= 'NZD';

$config['countries']['CR']				= new stdClass();
$config['countries']['CR']->code		= 'CR';
$config['countries']['CR']->code_3		= 'CRI';
$config['countries']['CR']->number		= '188';
$config['countries']['CR']->label		= 'Costa Rica';
$config['countries']['CR']->continent	= 'NA';
$config['countries']['CR']->currency	= 'CRC';

$config['countries']['HR']				= new stdClass();
$config['countries']['HR']->code		= 'HR';
$config['countries']['HR']->code_3		= 'HRV';
$config['countries']['HR']->number		= '191';
$config['countries']['HR']->label		= 'Croatia';
$config['countries']['HR']->continent	= 'EU';
$config['countries']['HR']->currency	= 'HRK';

$config['countries']['CU']				= new stdClass();
$config['countries']['CU']->code		= 'CU';
$config['countries']['CU']->code_3		= 'CUB';
$config['countries']['CU']->number		= '192';
$config['countries']['CU']->label		= 'Cuba';
$config['countries']['CU']->continent	= 'NA';
$config['countries']['CU']->currency	= 'CUP';

$config['countries']['CW']				= new stdClass();
$config['countries']['CW']->code		= 'CW';
$config['countries']['CW']->code_3		= 'CUW';
$config['countries']['CW']->number		= '531';
$config['countries']['CW']->label		= 'Curaçao';
$config['countries']['CW']->continent	= '';
$config['countries']['CW']->currency	= 'ANG';

$config['countries']['CY']				= new stdClass();
$config['countries']['CY']->code		= 'CY';
$config['countries']['CY']->code_3		= 'CYP';
$config['countries']['CY']->number		= '196';
$config['countries']['CY']->label		= 'Cyprus';
$config['countries']['CY']->continent	= 'AS';
$config['countries']['CY']->currency	= 'EUR';

$config['countries']['CZ']				= new stdClass();
$config['countries']['CZ']->code		= 'CZ';
$config['countries']['CZ']->code_3		= 'CZE';
$config['countries']['CZ']->number		= '203';
$config['countries']['CZ']->label		= 'Czech Republic';
$config['countries']['CZ']->continent	= 'EU';
$config['countries']['CZ']->currency	= 'CZK';

$config['countries']['CI']				= new stdClass();
$config['countries']['CI']->code		= 'CI';
$config['countries']['CI']->code_3		= 'CIV';
$config['countries']['CI']->number		= '384';
$config['countries']['CI']->label		= 'Côte D’Ivoire';
$config['countries']['CI']->continent	= 'AF';
$config['countries']['CI']->currency	= 'XAF';

$config['countries']['DK']				= new stdClass();
$config['countries']['DK']->code		= 'DK';
$config['countries']['DK']->code_3		= 'DNK';
$config['countries']['DK']->number		= '208';
$config['countries']['DK']->label		= 'Denmark';
$config['countries']['DK']->continent	= 'EU';
$config['countries']['DK']->currency	= 'DKK';

$config['countries']['DJ']				= new stdClass();
$config['countries']['DJ']->code		= 'DJ';
$config['countries']['DJ']->code_3		= 'DJI';
$config['countries']['DJ']->number		= '262';
$config['countries']['DJ']->label		= 'Djibouti';
$config['countries']['DJ']->continent	= 'AF';
$config['countries']['DJ']->currency	= 'DJF';

$config['countries']['DM']				= new stdClass();
$config['countries']['DM']->code		= 'DM';
$config['countries']['DM']->code_3		= 'DMA';
$config['countries']['DM']->number		= '212';
$config['countries']['DM']->label		= 'Dominica';
$config['countries']['DM']->continent	= 'NA';
$config['countries']['DM']->currency	= 'DKK';

$config['countries']['DO']				= new stdClass();
$config['countries']['DO']->code		= 'DO';
$config['countries']['DO']->code_3		= 'DOM';
$config['countries']['DO']->number		= '214';
$config['countries']['DO']->label		= 'Dominican Republic';
$config['countries']['DO']->continent	= 'NA';
$config['countries']['DO']->currency	= 'DOP';

$config['countries']['EC']				= new stdClass();
$config['countries']['EC']->code		= 'EC';
$config['countries']['EC']->code_3		= 'ECU';
$config['countries']['EC']->number		= '218';
$config['countries']['EC']->label		= 'Ecuador';
$config['countries']['EC']->continent	= 'SA';
$config['countries']['EC']->currency	= 'USD';

$config['countries']['EG']				= new stdClass();
$config['countries']['EG']->code		= 'EG';
$config['countries']['EG']->code_3		= 'EGY';
$config['countries']['EG']->number		= '818';
$config['countries']['EG']->label		= 'Egypt';
$config['countries']['EG']->continent	= 'AF';
$config['countries']['EG']->currency	= 'EGP';

$config['countries']['SV']				= new stdClass();
$config['countries']['SV']->code		= 'SV';
$config['countries']['SV']->code_3		= 'SLV';
$config['countries']['SV']->number		= '222';
$config['countries']['SV']->label		= 'El Salvador';
$config['countries']['SV']->continent	= 'NA';
$config['countries']['SV']->currency	= 'USD';

$config['countries']['GQ']				= new stdClass();
$config['countries']['GQ']->code		= 'GQ';
$config['countries']['GQ']->code_3		= 'GNQ';
$config['countries']['GQ']->number		= '226';
$config['countries']['GQ']->label		= 'Equatorial Guinea';
$config['countries']['GQ']->continent	= 'AF';
$config['countries']['GQ']->currency	= 'XAF';

$config['countries']['EE']				= new stdClass();
$config['countries']['EE']->code		= 'EE';
$config['countries']['EE']->code_3		= 'EST';
$config['countries']['EE']->number		= '233';
$config['countries']['EE']->label		= 'Estonia';
$config['countries']['EE']->continent	= 'EU';
$config['countries']['EE']->currency	= 'EEK';

$config['countries']['ET']				= new stdClass();
$config['countries']['ET']->code		= 'ET';
$config['countries']['ET']->code_3		= 'ETH';
$config['countries']['ET']->number		= '231';
$config['countries']['ET']->label		= 'Ethiopia';
$config['countries']['ET']->continent	= 'AF';
$config['countries']['ET']->currency	= 'ETB';

$config['countries']['FK']				= new stdClass();
$config['countries']['FK']->code		= 'FK';
$config['countries']['FK']->code_3		= 'FLK';
$config['countries']['FK']->number		= '238';
$config['countries']['FK']->label		= 'Falkland Islands (Malvinas)';
$config['countries']['FK']->continent	= 'SA';
$config['countries']['FK']->currency	= 'FKP';

$config['countries']['FO']				= new stdClass();
$config['countries']['FO']->code		= 'FO';
$config['countries']['FO']->code_3		= 'FRO';
$config['countries']['FO']->number		= '234';
$config['countries']['FO']->label		= 'Faroe Islands';
$config['countries']['FO']->continent	= 'EU';
$config['countries']['FO']->currency	= 'DKK';

$config['countries']['FJ']				= new stdClass();
$config['countries']['FJ']->code		= 'FJ';
$config['countries']['FJ']->code_3		= 'FJI';
$config['countries']['FJ']->number		= '242';
$config['countries']['FJ']->label		= 'Fiji';
$config['countries']['FJ']->continent	= 'OC';
$config['countries']['FJ']->currency	= 'FJD';

$config['countries']['FI']				= new stdClass();
$config['countries']['FI']->code		= 'FI';
$config['countries']['FI']->code_3		= 'FIN';
$config['countries']['FI']->number		= '246';
$config['countries']['FI']->label		= 'Finland';
$config['countries']['FI']->continent	= 'EU';
$config['countries']['FI']->currency	= 'EUR';

$config['countries']['FR']				= new stdClass();
$config['countries']['FR']->code		= 'FR';
$config['countries']['FR']->code_3		= 'FRA';
$config['countries']['FR']->number		= '250';
$config['countries']['FR']->label		= 'France';
$config['countries']['FR']->continent	= 'EU';
$config['countries']['FR']->currency	= 'EUR';

$config['countries']['GF']				= new stdClass();
$config['countries']['GF']->code		= 'GF';
$config['countries']['GF']->code_3		= 'GUF';
$config['countries']['GF']->number		= '254';
$config['countries']['GF']->label		= 'French Guiana';
$config['countries']['GF']->continent	= 'SA';
$config['countries']['GF']->currency	= 'EUR';

$config['countries']['PF']				= new stdClass();
$config['countries']['PF']->code		= 'PF';
$config['countries']['PF']->code_3		= 'PYF';
$config['countries']['PF']->number		= '258';
$config['countries']['PF']->label		= 'French Polynesia';
$config['countries']['PF']->continent	= 'OC';
$config['countries']['PF']->currency	= 'XPF';

$config['countries']['TF']				= new stdClass();
$config['countries']['TF']->code		= 'TF';
$config['countries']['TF']->code_3		= 'ATF';
$config['countries']['TF']->number		= '260';
$config['countries']['TF']->label		= 'French Southern Territories';
$config['countries']['TF']->continent	= 'AN';
$config['countries']['TF']->currency	= 'EUR';

$config['countries']['GA']				= new stdClass();
$config['countries']['GA']->code		= 'GA';
$config['countries']['GA']->code_3		= 'GAB';
$config['countries']['GA']->number		= '266';
$config['countries']['GA']->label		= 'Gabon';
$config['countries']['GA']->continent	= 'AF';
$config['countries']['GA']->currency	= 'XAF';

$config['countries']['GM']				= new stdClass();
$config['countries']['GM']->code		= 'GM';
$config['countries']['GM']->code_3		= 'GMB';
$config['countries']['GM']->number		= '270';
$config['countries']['GM']->label		= 'Gambia';
$config['countries']['GM']->continent	= 'AF';
$config['countries']['GM']->currency	= 'GMD';

$config['countries']['GE']				= new stdClass();
$config['countries']['GE']->code		= 'GE';
$config['countries']['GE']->code_3		= 'GEO';
$config['countries']['GE']->number		= '268';
$config['countries']['GE']->label		= 'Georgia';
$config['countries']['GE']->continent	= 'AS';
$config['countries']['GE']->currency	= 'GEL';

$config['countries']['DE']				= new stdClass();
$config['countries']['DE']->code		= 'DE';
$config['countries']['DE']->code_3		= 'DEU';
$config['countries']['DE']->number		= '276';
$config['countries']['DE']->label		= 'Germany';
$config['countries']['DE']->continent	= 'EU';
$config['countries']['DE']->currency	= 'EUR';

$config['countries']['GH']				= new stdClass();
$config['countries']['GH']->code		= 'GH';
$config['countries']['GH']->code_3		= 'GHA';
$config['countries']['GH']->number		= '288';
$config['countries']['GH']->label		= 'Ghana';
$config['countries']['GH']->continent	= 'AF';
$config['countries']['GH']->currency	= 'GHS';

$config['countries']['GI']				= new stdClass();
$config['countries']['GI']->code		= 'GI';
$config['countries']['GI']->code_3		= 'GIB';
$config['countries']['GI']->number		= '292';
$config['countries']['GI']->label		= 'Gibraltar';
$config['countries']['GI']->continent	= 'EU';
$config['countries']['GI']->currency	= 'GIP';

$config['countries']['GR']				= new stdClass();
$config['countries']['GR']->code		= 'GR';
$config['countries']['GR']->code_3		= 'GRC';
$config['countries']['GR']->number		= '300';
$config['countries']['GR']->label		= 'Greece';
$config['countries']['GR']->continent	= 'EU';
$config['countries']['GR']->currency	= 'EUR';

$config['countries']['GL']				= new stdClass();
$config['countries']['GL']->code		= 'GL';
$config['countries']['GL']->code_3		= 'GRL';
$config['countries']['GL']->number		= '304';
$config['countries']['GL']->label		= 'Greenland';
$config['countries']['GL']->continent	= 'NA';
$config['countries']['GL']->currency	= 'DKK';

$config['countries']['GD']				= new stdClass();
$config['countries']['GD']->code		= 'GD';
$config['countries']['GD']->code_3		= 'GRD';
$config['countries']['GD']->number		= '308';
$config['countries']['GD']->label		= 'Grenada';
$config['countries']['GD']->continent	= 'NA';
$config['countries']['GD']->currency	= 'XCD';

$config['countries']['GP']				= new stdClass();
$config['countries']['GP']->code		= 'GP';
$config['countries']['GP']->code_3		= 'GLP';
$config['countries']['GP']->number		= '312';
$config['countries']['GP']->label		= 'Guadeloupe';
$config['countries']['GP']->continent	= 'NA';
$config['countries']['GP']->currency	= 'EUR';

$config['countries']['GU']				= new stdClass();
$config['countries']['GU']->code		= 'GU';
$config['countries']['GU']->code_3		= 'GUM';
$config['countries']['GU']->number		= '316';
$config['countries']['GU']->label		= 'Guam';
$config['countries']['GU']->continent	= 'OC';
$config['countries']['GU']->currency	= 'USD';

$config['countries']['GT']				= new stdClass();
$config['countries']['GT']->code		= 'GT';
$config['countries']['GT']->code_3		= 'GTM';
$config['countries']['GT']->number		= '320';
$config['countries']['GT']->label		= 'Guatemala';
$config['countries']['GT']->continent	= 'NA';
$config['countries']['GT']->currency	= 'GTQ';

$config['countries']['GG']				= new stdClass();
$config['countries']['GG']->code		= 'GG';
$config['countries']['GG']->code_3		= 'GGY';
$config['countries']['GG']->number		= '831';
$config['countries']['GG']->label		= 'Guernsey';
$config['countries']['GG']->continent	= 'EU';
$config['countries']['GG']->currency	= 'GBP';

$config['countries']['GN']				= new stdClass();
$config['countries']['GN']->code		= 'GN';
$config['countries']['GN']->code_3		= 'GIN';
$config['countries']['GN']->number		= '324';
$config['countries']['GN']->label		= 'Guinea';
$config['countries']['GN']->continent	= 'AF';
$config['countries']['GN']->currency	= 'GNF';

$config['countries']['GW']				= new stdClass();
$config['countries']['GW']->code		= 'GW';
$config['countries']['GW']->code_3		= 'GNB';
$config['countries']['GW']->number		= '624';
$config['countries']['GW']->label		= 'Guinea-Bissau';
$config['countries']['GW']->continent	= 'AF';
$config['countries']['GW']->currency	= 'XAF';

$config['countries']['GY']				= new stdClass();
$config['countries']['GY']->code		= 'GY';
$config['countries']['GY']->code_3		= 'GUY';
$config['countries']['GY']->number		= '328';
$config['countries']['GY']->label		= 'Guyana';
$config['countries']['GY']->continent	= 'SA';
$config['countries']['GY']->currency	= 'GYD';

$config['countries']['HT']				= new stdClass();
$config['countries']['HT']->code		= 'HT';
$config['countries']['HT']->code_3		= 'HTI';
$config['countries']['HT']->number		= '332';
$config['countries']['HT']->label		= 'Haiti';
$config['countries']['HT']->continent	= 'NA';
$config['countries']['HT']->currency	= 'HTG';

$config['countries']['HM']				= new stdClass();
$config['countries']['HM']->code		= 'HM';
$config['countries']['HM']->code_3		= 'HMD';
$config['countries']['HM']->number		= '334';
$config['countries']['HM']->label		= 'Heard and McDonald Islands';
$config['countries']['HM']->continent	= 'AN';
$config['countries']['HM']->currency	= 'AUD';

$config['countries']['VA']				= new stdClass();
$config['countries']['VA']->code		= 'VA';
$config['countries']['VA']->code_3		= 'VAT';
$config['countries']['VA']->number		= '336';
$config['countries']['VA']->label		= 'Holy See (Vatican City State)';
$config['countries']['VA']->continent	= 'EU';
$config['countries']['VA']->currency	= 'EUR';

$config['countries']['HN']				= new stdClass();
$config['countries']['HN']->code		= 'HN';
$config['countries']['HN']->code_3		= 'HND';
$config['countries']['HN']->number		= '340';
$config['countries']['HN']->label		= 'Honduras';
$config['countries']['HN']->continent	= 'NA';
$config['countries']['HN']->currency	= 'HNL';

$config['countries']['HK']				= new stdClass();
$config['countries']['HK']->code		= 'HK';
$config['countries']['HK']->code_3		= 'HKG';
$config['countries']['HK']->number		= '344';
$config['countries']['HK']->label		= 'Hong Kong';
$config['countries']['HK']->continent	= 'AS';
$config['countries']['HK']->currency	= 'HKD';

$config['countries']['HU']				= new stdClass();
$config['countries']['HU']->code		= 'HU';
$config['countries']['HU']->code_3		= 'HUN';
$config['countries']['HU']->number		= '348';
$config['countries']['HU']->label		= 'Hungary';
$config['countries']['HU']->continent	= 'EU';
$config['countries']['HU']->currency	= 'HUF';

$config['countries']['IS']				= new stdClass();
$config['countries']['IS']->code		= 'IS';
$config['countries']['IS']->code_3		= 'ISL';
$config['countries']['IS']->number		= '352';
$config['countries']['IS']->label		= 'Iceland';
$config['countries']['IS']->continent	= 'EU';
$config['countries']['IS']->currency	= 'ISK';

$config['countries']['IN']				= new stdClass();
$config['countries']['IN']->code		= 'IN';
$config['countries']['IN']->code_3		= 'IND';
$config['countries']['IN']->number		= '356';
$config['countries']['IN']->label		= 'India';
$config['countries']['IN']->continent	= 'AS';
$config['countries']['IN']->currency	= 'INR';

$config['countries']['ID']				= new stdClass();
$config['countries']['ID']->code		= 'ID';
$config['countries']['ID']->code_3		= 'IDN';
$config['countries']['ID']->number		= '360';
$config['countries']['ID']->label		= 'Indonesia';
$config['countries']['ID']->continent	= 'AS';
$config['countries']['ID']->currency	= 'IDR';

$config['countries']['IR']				= new stdClass();
$config['countries']['IR']->code		= 'IR';
$config['countries']['IR']->code_3		= 'IRN';
$config['countries']['IR']->number		= '364';
$config['countries']['IR']->label		= 'Iran (Islamic Republic of)';
$config['countries']['IR']->continent	= 'AS';
$config['countries']['IR']->currency	= 'IRR';

$config['countries']['IQ']				= new stdClass();
$config['countries']['IQ']->code		= 'IQ';
$config['countries']['IQ']->code_3		= 'IRQ';
$config['countries']['IQ']->number		= '368';
$config['countries']['IQ']->label		= 'Iraq';
$config['countries']['IQ']->continent	= 'AS';
$config['countries']['IQ']->currency	= 'IQD';

$config['countries']['IE']				= new stdClass();
$config['countries']['IE']->code		= 'IE';
$config['countries']['IE']->code_3		= 'IRL';
$config['countries']['IE']->number		= '372';
$config['countries']['IE']->label		= 'Ireland';
$config['countries']['IE']->continent	= 'EU';
$config['countries']['IE']->currency	= 'EUR';

$config['countries']['IM']				= new stdClass();
$config['countries']['IM']->code		= 'IM';
$config['countries']['IM']->code_3		= 'IMN';
$config['countries']['IM']->number		= '833';
$config['countries']['IM']->label		= 'Isle of Man';
$config['countries']['IM']->continent	= 'EU';
$config['countries']['IM']->currency	= 'GBP';

$config['countries']['IL']				= new stdClass();
$config['countries']['IL']->code		= 'IL';
$config['countries']['IL']->code_3		= 'ISR';
$config['countries']['IL']->number		= '376';
$config['countries']['IL']->label		= 'Israel';
$config['countries']['IL']->continent	= 'AS';
$config['countries']['IL']->currency	= 'ILS';

$config['countries']['IT']				= new stdClass();
$config['countries']['IT']->code		= 'IT';
$config['countries']['IT']->code_3		= 'ITA';
$config['countries']['IT']->number		= '380';
$config['countries']['IT']->label		= 'Italy';
$config['countries']['IT']->continent	= 'EU';
$config['countries']['IT']->currency	= 'EUR';

$config['countries']['JM']				= new stdClass();
$config['countries']['JM']->code		= 'JM';
$config['countries']['JM']->code_3		= 'JAM';
$config['countries']['JM']->number		= '388';
$config['countries']['JM']->label		= 'Jamaica';
$config['countries']['JM']->continent	= 'NA';
$config['countries']['JM']->currency	= 'JMD';

$config['countries']['JP']				= new stdClass();
$config['countries']['JP']->code		= 'JP';
$config['countries']['JP']->code_3		= 'JPN';
$config['countries']['JP']->number		= '392';
$config['countries']['JP']->label		= 'Japan';
$config['countries']['JP']->continent	= 'AS';
$config['countries']['JP']->currency	= 'JPY';

$config['countries']['JE']				= new stdClass();
$config['countries']['JE']->code		= 'JE';
$config['countries']['JE']->code_3		= 'JEY';
$config['countries']['JE']->number		= '832';
$config['countries']['JE']->label		= 'Jersey';
$config['countries']['JE']->continent	= 'EU';
$config['countries']['JE']->currency	= 'JEP';

$config['countries']['JO']				= new stdClass();
$config['countries']['JO']->code		= 'JO';
$config['countries']['JO']->code_3		= 'JOR';
$config['countries']['JO']->number		= '400';
$config['countries']['JO']->label		= 'Jordan';
$config['countries']['JO']->continent	= 'AS';
$config['countries']['JO']->currency	= 'JOD';

$config['countries']['KZ']				= new stdClass();
$config['countries']['KZ']->code		= 'KZ';
$config['countries']['KZ']->code_3		= 'KAZ';
$config['countries']['KZ']->number		= '398';
$config['countries']['KZ']->label		= 'Kazakhstan';
$config['countries']['KZ']->continent	= 'AS';
$config['countries']['KZ']->currency	= 'KZT';

$config['countries']['KE']				= new stdClass();
$config['countries']['KE']->code		= 'KE';
$config['countries']['KE']->code_3		= 'KEN';
$config['countries']['KE']->number		= '404';
$config['countries']['KE']->label		= 'Kenya';
$config['countries']['KE']->continent	= 'AF';
$config['countries']['KE']->currency	= 'KES';

$config['countries']['KI']				= new stdClass();
$config['countries']['KI']->code		= 'KI';
$config['countries']['KI']->code_3		= 'KIR';
$config['countries']['KI']->number		= '296';
$config['countries']['KI']->label		= 'Kiribati';
$config['countries']['KI']->continent	= 'OC';
$config['countries']['KI']->currency	= 'AUD';

$config['countries']['KP']				= new stdClass();
$config['countries']['KP']->code		= 'KP';
$config['countries']['KP']->code_3		= 'PRK';
$config['countries']['KP']->number		= '408';
$config['countries']['KP']->label		= 'Korea, Democratic People\'s Republic of (North Korea)';
$config['countries']['KP']->continent	= 'AS';
$config['countries']['KP']->currency	= 'KPW';

$config['countries']['KR']				= new stdClass();
$config['countries']['KR']->code		= 'KR';
$config['countries']['KR']->code_3		= 'KOR';
$config['countries']['KR']->number		= '410';
$config['countries']['KR']->label		= 'Korea, Republic of (South Korea)';
$config['countries']['KR']->continent	= 'AS';
$config['countries']['KR']->currency	= 'KRW';

$config['countries']['KW']				= new stdClass();
$config['countries']['KW']->code		= 'KW';
$config['countries']['KW']->code_3		= 'KWT';
$config['countries']['KW']->number		= '414';
$config['countries']['KW']->label		= 'Kuwait';
$config['countries']['KW']->continent	= 'AS';
$config['countries']['KW']->currency	= 'KWD';

$config['countries']['KG']				= new stdClass();
$config['countries']['KG']->code		= 'KG';
$config['countries']['KG']->code_3		= 'KGZ';
$config['countries']['KG']->number		= '417';
$config['countries']['KG']->label		= 'Kyrgyzstan';
$config['countries']['KG']->continent	= 'AS';
$config['countries']['KG']->currency	= 'KGS';

$config['countries']['LA']				= new stdClass();
$config['countries']['LA']->code		= 'LA';
$config['countries']['LA']->code_3		= 'LAO';
$config['countries']['LA']->number		= '418';
$config['countries']['LA']->label		= 'Lao People\'s Democratic Republic';
$config['countries']['LA']->continent	= 'AS';
$config['countries']['LA']->currency	= 'LAK';

$config['countries']['LV']				= new stdClass();
$config['countries']['LV']->code		= 'LV';
$config['countries']['LV']->code_3		= 'LVA';
$config['countries']['LV']->number		= '428';
$config['countries']['LV']->label		= 'Latvia';
$config['countries']['LV']->continent	= 'EU';
$config['countries']['LV']->currency	= 'LVL';

$config['countries']['LB']				= new stdClass();
$config['countries']['LB']->code		= 'LB';
$config['countries']['LB']->code_3		= 'LBN';
$config['countries']['LB']->number		= '422';
$config['countries']['LB']->label		= 'Lebanon';
$config['countries']['LB']->continent	= 'AS';
$config['countries']['LB']->currency	= 'LBP';

$config['countries']['LS']				= new stdClass();
$config['countries']['LS']->code		= 'LS';
$config['countries']['LS']->code_3		= 'LSO';
$config['countries']['LS']->number		= '426';
$config['countries']['LS']->label		= 'Lesotho';
$config['countries']['LS']->continent	= 'AF';
$config['countries']['LS']->currency	= 'LSL';

$config['countries']['LR']				= new stdClass();
$config['countries']['LR']->code		= 'LR';
$config['countries']['LR']->code_3		= 'LBR';
$config['countries']['LR']->number		= '430';
$config['countries']['LR']->label		= 'Liberia';
$config['countries']['LR']->continent	= 'AF';
$config['countries']['LR']->currency	= 'LRD';

$config['countries']['LY']				= new stdClass();
$config['countries']['LY']->code		= 'LY';
$config['countries']['LY']->code_3		= 'LBY';
$config['countries']['LY']->number		= '434';
$config['countries']['LY']->label		= 'Libya';
$config['countries']['LY']->continent	= 'AF';
$config['countries']['LY']->currency	= 'LYD';

$config['countries']['LI']				= new stdClass();
$config['countries']['LI']->code		= 'LI';
$config['countries']['LI']->code_3		= 'LIE';
$config['countries']['LI']->number		= '438';
$config['countries']['LI']->label		= 'Liechtenstein';
$config['countries']['LI']->continent	= 'EU';
$config['countries']['LI']->currency	= 'CHF';

$config['countries']['LT']				= new stdClass();
$config['countries']['LT']->code		= 'LT';
$config['countries']['LT']->code_3		= 'LTU';
$config['countries']['LT']->number		= '440';
$config['countries']['LT']->label		= 'Lithuania';
$config['countries']['LT']->continent	= 'EU';
$config['countries']['LT']->currency	= 'LTL';

$config['countries']['LU']				= new stdClass();
$config['countries']['LU']->code		= 'LU';
$config['countries']['LU']->code_3		= 'LUX';
$config['countries']['LU']->number		= '442';
$config['countries']['LU']->label		= 'Luxembourg';
$config['countries']['LU']->continent	= 'EU';
$config['countries']['LU']->currency	= 'EUR';

$config['countries']['MO']				= new stdClass();
$config['countries']['MO']->code		= 'MO';
$config['countries']['MO']->code_3		= 'MAC';
$config['countries']['MO']->number		= '446';
$config['countries']['MO']->label		= 'Macau';
$config['countries']['MO']->continent	= 'AS';
$config['countries']['MO']->currency	= 'MOP';

$config['countries']['MK']				= new stdClass();
$config['countries']['MK']->code		= 'MK';
$config['countries']['MK']->code_3		= 'MKD';
$config['countries']['MK']->number		= '807';
$config['countries']['MK']->label		= 'Macedonia, The Former Yugoslav Republic of';
$config['countries']['MK']->continent	= 'EU';
$config['countries']['MK']->currency	= 'MKD';

$config['countries']['MG']				= new stdClass();
$config['countries']['MG']->code		= 'MG';
$config['countries']['MG']->code_3		= 'MDG';
$config['countries']['MG']->number		= '450';
$config['countries']['MG']->label		= 'Madagascar';
$config['countries']['MG']->continent	= 'AF';
$config['countries']['MG']->currency	= 'MGA';

$config['countries']['MW']				= new stdClass();
$config['countries']['MW']->code		= 'MW';
$config['countries']['MW']->code_3		= 'MWI';
$config['countries']['MW']->number		= '454';
$config['countries']['MW']->label		= 'Malawi';
$config['countries']['MW']->continent	= 'AF';
$config['countries']['MW']->currency	= 'MWK';

$config['countries']['MY']				= new stdClass();
$config['countries']['MY']->code		= 'MY';
$config['countries']['MY']->code_3		= 'MYS';
$config['countries']['MY']->number		= '458';
$config['countries']['MY']->label		= 'Malaysia';
$config['countries']['MY']->continent	= 'AS';
$config['countries']['MY']->currency	= 'MYR';

$config['countries']['MV']				= new stdClass();
$config['countries']['MV']->code		= 'MV';
$config['countries']['MV']->code_3		= 'MDV';
$config['countries']['MV']->number		= '462';
$config['countries']['MV']->label		= 'Maldives';
$config['countries']['MV']->continent	= 'AS';
$config['countries']['MV']->currency	= 'MVR';

$config['countries']['ML']				= new stdClass();
$config['countries']['ML']->code		= 'ML';
$config['countries']['ML']->code_3		= 'MLI';
$config['countries']['ML']->number		= '466';
$config['countries']['ML']->label		= 'Mali';
$config['countries']['ML']->continent	= 'AF';
$config['countries']['ML']->currency	= 'XAF';

$config['countries']['MT']				= new stdClass();
$config['countries']['MT']->code		= 'MT';
$config['countries']['MT']->code_3		= 'MLT';
$config['countries']['MT']->number		= '470';
$config['countries']['MT']->label		= 'Malta';
$config['countries']['MT']->continent	= 'EU';
$config['countries']['MT']->currency	= 'EUR';

$config['countries']['MH']				= new stdClass();
$config['countries']['MH']->code		= 'MH';
$config['countries']['MH']->code_3		= 'MHL';
$config['countries']['MH']->number		= '584';
$config['countries']['MH']->label		= 'Marshall Islands';
$config['countries']['MH']->continent	= 'OC';
$config['countries']['MH']->currency	= 'USD';

$config['countries']['MQ']				= new stdClass();
$config['countries']['MQ']->code		= 'MQ';
$config['countries']['MQ']->code_3		= 'MTQ';
$config['countries']['MQ']->number		= '474';
$config['countries']['MQ']->label		= 'Martinique';
$config['countries']['MQ']->continent	= 'NA';
$config['countries']['MQ']->currency	= 'EUR';

$config['countries']['MR']				= new stdClass();
$config['countries']['MR']->code		= 'MR';
$config['countries']['MR']->code_3		= 'MRT';
$config['countries']['MR']->number		= '478';
$config['countries']['MR']->label		= 'Mauritania';
$config['countries']['MR']->continent	= 'AF';
$config['countries']['MR']->currency	= 'MRO';

$config['countries']['MU']				= new stdClass();
$config['countries']['MU']->code		= 'MU';
$config['countries']['MU']->code_3		= 'MUS';
$config['countries']['MU']->number		= '480';
$config['countries']['MU']->label		= 'Mauritius';
$config['countries']['MU']->continent	= 'AF';
$config['countries']['MU']->currency	= 'MUR';

$config['countries']['YT']				= new stdClass();
$config['countries']['YT']->code		= 'YT';
$config['countries']['YT']->code_3		= 'MYT';
$config['countries']['YT']->number		= '175';
$config['countries']['YT']->label		= 'Mayotte';
$config['countries']['YT']->continent	= 'AF';
$config['countries']['YT']->currency	= 'EUR';

$config['countries']['MX']				= new stdClass();
$config['countries']['MX']->code		= 'MX';
$config['countries']['MX']->code_3		= 'MEX';
$config['countries']['MX']->number		= '484';
$config['countries']['MX']->label		= 'Mexico';
$config['countries']['MX']->continent	= 'NA';
$config['countries']['MX']->currency	= 'MXN';

$config['countries']['FM']				= new stdClass();
$config['countries']['FM']->code		= 'FM';
$config['countries']['FM']->code_3		= 'FSM';
$config['countries']['FM']->number		= '583';
$config['countries']['FM']->label		= 'Micronesia, Federated States of';
$config['countries']['FM']->continent	= 'OC';
$config['countries']['FM']->currency	= 'USD';

$config['countries']['MD']				= new stdClass();
$config['countries']['MD']->code		= 'MD';
$config['countries']['MD']->code_3		= 'MDA';
$config['countries']['MD']->number		= '498';
$config['countries']['MD']->label		= 'Moldova, Republic of';
$config['countries']['MD']->continent	= 'EU';
$config['countries']['MD']->currency	= 'MDL';

$config['countries']['MC']				= new stdClass();
$config['countries']['MC']->code		= 'MC';
$config['countries']['MC']->code_3		= 'MCO';
$config['countries']['MC']->number		= '492';
$config['countries']['MC']->label		= 'Monaco';
$config['countries']['MC']->continent	= 'EU';
$config['countries']['MC']->currency	= 'EUR';

$config['countries']['MN']				= new stdClass();
$config['countries']['MN']->code		= 'MN';
$config['countries']['MN']->code_3		= 'MNG';
$config['countries']['MN']->number		= '496';
$config['countries']['MN']->label		= 'Mongolia';
$config['countries']['MN']->continent	= 'AS';
$config['countries']['MN']->currency	= 'MNT';

$config['countries']['ME']				= new stdClass();
$config['countries']['ME']->code		= 'ME';
$config['countries']['ME']->code_3		= 'MNE';
$config['countries']['ME']->number		= '499';
$config['countries']['ME']->label		= 'Montenegro';
$config['countries']['ME']->continent	= 'EU';
$config['countries']['ME']->currency	= 'EUR';

$config['countries']['MS']				= new stdClass();
$config['countries']['MS']->code		= 'MS';
$config['countries']['MS']->code_3		= 'MSR';
$config['countries']['MS']->number		= '500';
$config['countries']['MS']->label		= 'Montserrat';
$config['countries']['MS']->continent	= 'NA';
$config['countries']['MS']->currency	= 'XCD';

$config['countries']['MA']				= new stdClass();
$config['countries']['MA']->code		= 'MA';
$config['countries']['MA']->code_3		= 'MAR';
$config['countries']['MA']->number		= '504';
$config['countries']['MA']->label		= 'Morocco';
$config['countries']['MA']->continent	= 'AF';
$config['countries']['MA']->currency	= 'MAD';

$config['countries']['MZ']				= new stdClass();
$config['countries']['MZ']->code		= 'MZ';
$config['countries']['MZ']->code_3		= 'MOZ';
$config['countries']['MZ']->number		= '508';
$config['countries']['MZ']->label		= 'Mozambique';
$config['countries']['MZ']->continent	= 'AF';
$config['countries']['MZ']->currency	= 'MZN';

$config['countries']['MM']				= new stdClass();
$config['countries']['MM']->code		= 'MM';
$config['countries']['MM']->code_3		= 'MMR';
$config['countries']['MM']->number		= '104';
$config['countries']['MM']->label		= 'Myanmar';
$config['countries']['MM']->continent	= 'AS';
$config['countries']['MM']->currency	= 'MMK';

$config['countries']['NA']				= new stdClass();
$config['countries']['NA']->code		= 'NA';
$config['countries']['NA']->code_3		= 'NAM';
$config['countries']['NA']->number		= '516';
$config['countries']['NA']->label		= 'Namibia';
$config['countries']['NA']->continent	= 'AF';
$config['countries']['NA']->currency	= 'NAD';

$config['countries']['NR']				= new stdClass();
$config['countries']['NR']->code		= 'NR';
$config['countries']['NR']->code_3		= 'NRU';
$config['countries']['NR']->number		= '520';
$config['countries']['NR']->label		= 'Nauru';
$config['countries']['NR']->continent	= 'OC';
$config['countries']['NR']->currency	= 'AUD';

$config['countries']['NP']				= new stdClass();
$config['countries']['NP']->code		= 'NP';
$config['countries']['NP']->code_3		= 'NPL';
$config['countries']['NP']->number		= '524';
$config['countries']['NP']->label		= 'Nepal';
$config['countries']['NP']->continent	= 'AS';
$config['countries']['NP']->currency	= 'NPR';

$config['countries']['NL']				= new stdClass();
$config['countries']['NL']->code		= 'NL';
$config['countries']['NL']->code_3		= 'NLD';
$config['countries']['NL']->number		= '528';
$config['countries']['NL']->label		= 'Netherlands';
$config['countries']['NL']->continent	= 'EU';
$config['countries']['NL']->currency	= 'ANG';

$config['countries']['NC']				= new stdClass();
$config['countries']['NC']->code		= 'NC';
$config['countries']['NC']->code_3		= 'NCL';
$config['countries']['NC']->number		= '540';
$config['countries']['NC']->label		= 'New Caledonia';
$config['countries']['NC']->continent	= 'OC';
$config['countries']['NC']->currency	= 'XPF';

$config['countries']['NZ']				= new stdClass();
$config['countries']['NZ']->code		= 'NZ';
$config['countries']['NZ']->code_3		= 'NZL';
$config['countries']['NZ']->number		= '554';
$config['countries']['NZ']->label		= 'New Zealand';
$config['countries']['NZ']->continent	= 'OC';
$config['countries']['NZ']->currency	= 'NZD';

$config['countries']['NI']				= new stdClass();
$config['countries']['NI']->code		= 'NI';
$config['countries']['NI']->code_3		= 'NIC';
$config['countries']['NI']->number		= '558';
$config['countries']['NI']->label		= 'Nicaragua';
$config['countries']['NI']->continent	= 'NA';
$config['countries']['NI']->currency	= 'NIO';

$config['countries']['NE']				= new stdClass();
$config['countries']['NE']->code		= 'NE';
$config['countries']['NE']->code_3		= 'NER';
$config['countries']['NE']->number		= '562';
$config['countries']['NE']->label		= 'Niger';
$config['countries']['NE']->continent	= 'AF';
$config['countries']['NE']->currency	= 'XAF';

$config['countries']['NG']				= new stdClass();
$config['countries']['NG']->code		= 'NG';
$config['countries']['NG']->code_3		= 'NGA';
$config['countries']['NG']->number		= '566';
$config['countries']['NG']->label		= 'Nigeria';
$config['countries']['NG']->continent	= 'AF';
$config['countries']['NG']->currency	= 'NGN';

$config['countries']['NU']				= new stdClass();
$config['countries']['NU']->code		= 'NU';
$config['countries']['NU']->code_3		= 'NIU';
$config['countries']['NU']->number		= '570';
$config['countries']['NU']->label		= 'Niue';
$config['countries']['NU']->continent	= 'OC';
$config['countries']['NU']->currency	= 'NZD';

$config['countries']['NF']				= new stdClass();
$config['countries']['NF']->code		= 'NF';
$config['countries']['NF']->code_3		= 'NFK';
$config['countries']['NF']->number		= '574';
$config['countries']['NF']->label		= 'Norfolk Island';
$config['countries']['NF']->continent	= 'OC';
$config['countries']['NF']->currency	= 'AUD';

$config['countries']['MP']				= new stdClass();
$config['countries']['MP']->code		= 'MP';
$config['countries']['MP']->code_3		= 'MNP';
$config['countries']['MP']->number		= '580';
$config['countries']['MP']->label		= 'Northern Mariana Islands';
$config['countries']['MP']->continent	= 'OC';
$config['countries']['MP']->currency	= 'USD';

$config['countries']['NO']				= new stdClass();
$config['countries']['NO']->code		= 'NO';
$config['countries']['NO']->code_3		= 'NOR';
$config['countries']['NO']->number		= '578';
$config['countries']['NO']->label		= 'Norway';
$config['countries']['NO']->continent	= 'EU';
$config['countries']['NO']->currency	= 'NOK';

$config['countries']['OM']				= new stdClass();
$config['countries']['OM']->code		= 'OM';
$config['countries']['OM']->code_3		= 'OMN';
$config['countries']['OM']->number		= '512';
$config['countries']['OM']->label		= 'Oman';
$config['countries']['OM']->continent	= 'AS';
$config['countries']['OM']->currency	= 'OMR';

$config['countries']['PK']				= new stdClass();
$config['countries']['PK']->code		= 'PK';
$config['countries']['PK']->code_3		= 'PAK';
$config['countries']['PK']->number		= '586';
$config['countries']['PK']->label		= 'Pakistan';
$config['countries']['PK']->continent	= 'AS';
$config['countries']['PK']->currency	= 'PKR';

$config['countries']['PW']				= new stdClass();
$config['countries']['PW']->code		= 'PW';
$config['countries']['PW']->code_3		= 'PLW';
$config['countries']['PW']->number		= '585';
$config['countries']['PW']->label		= 'Palau';
$config['countries']['PW']->continent	= 'OC';
$config['countries']['PW']->currency	= 'USD';

$config['countries']['PA']				= new stdClass();
$config['countries']['PA']->code		= 'PA';
$config['countries']['PA']->code_3		= 'PAN';
$config['countries']['PA']->number		= '591';
$config['countries']['PA']->label		= 'Panama';
$config['countries']['PA']->continent	= 'NA';
$config['countries']['PA']->currency	= 'PAB';

$config['countries']['PG']				= new stdClass();
$config['countries']['PG']->code		= 'PG';
$config['countries']['PG']->code_3		= 'PNG';
$config['countries']['PG']->number		= '598';
$config['countries']['PG']->label		= 'Papua New Guinea';
$config['countries']['PG']->continent	= 'OC';
$config['countries']['PG']->currency	= 'PGK';

$config['countries']['PY']				= new stdClass();
$config['countries']['PY']->code		= 'PY';
$config['countries']['PY']->code_3		= 'PRY';
$config['countries']['PY']->number		= '600';
$config['countries']['PY']->label		= 'Paraguay';
$config['countries']['PY']->continent	= 'SA';
$config['countries']['PY']->currency	= 'PYG';

$config['countries']['PE']				= new stdClass();
$config['countries']['PE']->code		= 'PE';
$config['countries']['PE']->code_3		= 'PER';
$config['countries']['PE']->number		= '604';
$config['countries']['PE']->label		= 'Peru';
$config['countries']['PE']->continent	= 'SA';
$config['countries']['PE']->currency	= 'PEN';

$config['countries']['PH']				= new stdClass();
$config['countries']['PH']->code		= 'PH';
$config['countries']['PH']->code_3		= 'PHL';
$config['countries']['PH']->number		= '608';
$config['countries']['PH']->label		= 'Philippines';
$config['countries']['PH']->continent	= 'AS';
$config['countries']['PH']->currency	= 'PHP';

$config['countries']['PN']				= new stdClass();
$config['countries']['PN']->code		= 'PN';
$config['countries']['PN']->code_3		= 'PCN';
$config['countries']['PN']->number		= '612';
$config['countries']['PN']->label		= 'Pitcairn';
$config['countries']['PN']->continent	= 'OC';
$config['countries']['PN']->currency	= 'NZD';

$config['countries']['PL']				= new stdClass();
$config['countries']['PL']->code		= 'PL';
$config['countries']['PL']->code_3		= 'POL';
$config['countries']['PL']->number		= '616';
$config['countries']['PL']->label		= 'Poland';
$config['countries']['PL']->continent	= 'EU';
$config['countries']['PL']->currency	= 'PLN';

$config['countries']['PT']				= new stdClass();
$config['countries']['PT']->code		= 'PT';
$config['countries']['PT']->code_3		= 'PRT';
$config['countries']['PT']->number		= '620';
$config['countries']['PT']->label		= 'Portugal';
$config['countries']['PT']->continent	= 'EU';
$config['countries']['PT']->currency	= 'EUR';

$config['countries']['PR']				= new stdClass();
$config['countries']['PR']->code		= 'PR';
$config['countries']['PR']->code_3		= 'PRI';
$config['countries']['PR']->number		= '630';
$config['countries']['PR']->label		= 'Puerto Rico';
$config['countries']['PR']->continent	= 'NA';
$config['countries']['PR']->currency	= 'USD';

$config['countries']['QA']				= new stdClass();
$config['countries']['QA']->code		= 'QA';
$config['countries']['QA']->code_3		= 'QAT';
$config['countries']['QA']->number		= '634';
$config['countries']['QA']->label		= 'Qatar';
$config['countries']['QA']->continent	= 'AS';
$config['countries']['QA']->currency	= 'QAR';

$config['countries']['RE']				= new stdClass();
$config['countries']['RE']->code		= 'RE';
$config['countries']['RE']->code_3		= 'REU';
$config['countries']['RE']->number		= '638';
$config['countries']['RE']->label		= 'Reunion';
$config['countries']['RE']->continent	= 'AF';
$config['countries']['RE']->currency	= 'EUR';

$config['countries']['RO']				= new stdClass();
$config['countries']['RO']->code		= 'RO';
$config['countries']['RO']->code_3		= 'ROU';
$config['countries']['RO']->number		= '642';
$config['countries']['RO']->label		= 'Romania';
$config['countries']['RO']->continent	= 'EU';
$config['countries']['RO']->currency	= 'RON';

$config['countries']['RU']				= new stdClass();
$config['countries']['RU']->code		= 'RU';
$config['countries']['RU']->code_3		= 'RUS';
$config['countries']['RU']->number		= '643';
$config['countries']['RU']->label		= 'Russian Federation';
$config['countries']['RU']->continent	= 'EU';
$config['countries']['RU']->currency	= 'RUB';

$config['countries']['RW']				= new stdClass();
$config['countries']['RW']->code		= 'RW';
$config['countries']['RW']->code_3		= 'RWA';
$config['countries']['RW']->number		= '646';
$config['countries']['RW']->label		= 'Rwanda';
$config['countries']['RW']->continent	= 'AF';
$config['countries']['RW']->currency	= 'RWF';

$config['countries']['BL']				= new stdClass();
$config['countries']['BL']->code		= 'BL';
$config['countries']['BL']->code_3		= 'BLM';
$config['countries']['BL']->number		= '652';
$config['countries']['BL']->label		= 'Saint Barthélemy';
$config['countries']['BL']->continent	= 'NA';
$config['countries']['BL']->currency	= 'EUR';

$config['countries']['SH']				= new stdClass();
$config['countries']['SH']->code		= 'SH';
$config['countries']['SH']->code_3		= 'SHN';
$config['countries']['SH']->number		= '654';
$config['countries']['SH']->label		= 'Saint Helena';
$config['countries']['SH']->continent	= 'AF';
$config['countries']['SH']->currency	= 'SHP';

$config['countries']['KN']				= new stdClass();
$config['countries']['KN']->code		= 'KN';
$config['countries']['KN']->code_3		= 'KNA';
$config['countries']['KN']->number		= '659';
$config['countries']['KN']->label		= 'Saint Kitts and Nevis';
$config['countries']['KN']->continent	= 'NA';
$config['countries']['KN']->currency	= 'XCD';

$config['countries']['LC']				= new stdClass();
$config['countries']['LC']->code		= 'LC';
$config['countries']['LC']->code_3		= 'LCA';
$config['countries']['LC']->number		= '662';
$config['countries']['LC']->label		= 'Saint Lucia';
$config['countries']['LC']->continent	= 'NA';
$config['countries']['LC']->currency	= 'XCD';

$config['countries']['MF']				= new stdClass();
$config['countries']['MF']->code		= 'MF';
$config['countries']['MF']->code_3		= 'MAF';
$config['countries']['MF']->number		= '663';
$config['countries']['MF']->label		= 'Saint Martin (French part)';
$config['countries']['MF']->continent	= 'NA';
$config['countries']['MF']->currency	= 'EUR';

$config['countries']['PM']				= new stdClass();
$config['countries']['PM']->code		= 'PM';
$config['countries']['PM']->code_3		= 'SPM';
$config['countries']['PM']->number		= '666';
$config['countries']['PM']->label		= 'Saint Pierre And Miquelon';
$config['countries']['PM']->continent	= 'NA';
$config['countries']['PM']->currency	= 'EUR';

$config['countries']['VC']				= new stdClass();
$config['countries']['VC']->code		= 'VC';
$config['countries']['VC']->code_3		= 'VCT';
$config['countries']['VC']->number		= '670';
$config['countries']['VC']->label		= 'Saint Vincent and the Grenadines';
$config['countries']['VC']->continent	= 'NA';
$config['countries']['VC']->currency	= 'XCD';

$config['countries']['WS']				= new stdClass();
$config['countries']['WS']->code		= 'WS';
$config['countries']['WS']->code_3		= 'WSM';
$config['countries']['WS']->number		= '882';
$config['countries']['WS']->label		= 'Samoa';
$config['countries']['WS']->continent	= 'OC';
$config['countries']['WS']->currency	= 'WST';

$config['countries']['SM']				= new stdClass();
$config['countries']['SM']->code		= 'SM';
$config['countries']['SM']->code_3		= 'SMR';
$config['countries']['SM']->number		= '674';
$config['countries']['SM']->label		= 'San Marino';
$config['countries']['SM']->continent	= 'EU';
$config['countries']['SM']->currency	= 'EUR';

$config['countries']['ST']				= new stdClass();
$config['countries']['ST']->code		= 'ST';
$config['countries']['ST']->code_3		= 'STP';
$config['countries']['ST']->number		= '678';
$config['countries']['ST']->label		= 'Sao Tome and Principe';
$config['countries']['ST']->continent	= 'AF';
$config['countries']['ST']->currency	= 'STD';

$config['countries']['SA']				= new stdClass();
$config['countries']['SA']->code		= 'SA';
$config['countries']['SA']->code_3		= 'SAU';
$config['countries']['SA']->number		= '682';
$config['countries']['SA']->label		= 'Saudi Arabia';
$config['countries']['SA']->continent	= 'AS';
$config['countries']['SA']->currency	= 'SAR';

$config['countries']['SN']				= new stdClass();
$config['countries']['SN']->code		= 'SN';
$config['countries']['SN']->code_3		= 'SEN';
$config['countries']['SN']->number		= '686';
$config['countries']['SN']->label		= 'Senegal';
$config['countries']['SN']->continent	= 'AF';
$config['countries']['SN']->currency	= 'XAF';

$config['countries']['RS']				= new stdClass();
$config['countries']['RS']->code		= 'RS';
$config['countries']['RS']->code_3		= 'SRB';
$config['countries']['RS']->number		= '688';
$config['countries']['RS']->label		= 'Serbia';
$config['countries']['RS']->continent	= 'EU';
$config['countries']['RS']->currency	= 'RSD';

$config['countries']['SC']				= new stdClass();
$config['countries']['SC']->code		= 'SC';
$config['countries']['SC']->code_3		= 'SYC';
$config['countries']['SC']->number		= '690';
$config['countries']['SC']->label		= 'Seychelles';
$config['countries']['SC']->continent	= 'AF';
$config['countries']['SC']->currency	= 'SCR';

$config['countries']['SL']				= new stdClass();
$config['countries']['SL']->code		= 'SL';
$config['countries']['SL']->code_3		= 'SLE';
$config['countries']['SL']->number		= '694';
$config['countries']['SL']->label		= 'Sierra Leone';
$config['countries']['SL']->continent	= 'AF';
$config['countries']['SL']->currency	= 'SLL';

$config['countries']['SG']				= new stdClass();
$config['countries']['SG']->code		= 'SG';
$config['countries']['SG']->code_3		= 'SGP';
$config['countries']['SG']->number		= '702';
$config['countries']['SG']->label		= 'Singapore';
$config['countries']['SG']->continent	= 'AS';
$config['countries']['SG']->currency	= 'SGD';

$config['countries']['SX']				= new stdClass();
$config['countries']['SX']->code		= 'SX';
$config['countries']['SX']->code_3		= 'SXM';
$config['countries']['SX']->number		= '534';
$config['countries']['SX']->label		= 'Sint Maarten (Dutch part)';
$config['countries']['SX']->continent	= '';
$config['countries']['SX']->currency	= 'ANG';

$config['countries']['SK']				= new stdClass();
$config['countries']['SK']->code		= 'SK';
$config['countries']['SK']->code_3		= 'SVK';
$config['countries']['SK']->number		= '703';
$config['countries']['SK']->label		= 'Slovakia (Slovak Republic)';
$config['countries']['SK']->continent	= 'EU';
$config['countries']['SK']->currency	= 'EUR';

$config['countries']['SI']				= new stdClass();
$config['countries']['SI']->code		= 'SI';
$config['countries']['SI']->code_3		= 'SVN';
$config['countries']['SI']->number		= '705';
$config['countries']['SI']->label		= 'Slovenia';
$config['countries']['SI']->continent	= 'EU';
$config['countries']['SI']->currency	= 'EUR';

$config['countries']['SB']				= new stdClass();
$config['countries']['SB']->code		= 'SB';
$config['countries']['SB']->code_3		= 'SLB';
$config['countries']['SB']->number		= '090';
$config['countries']['SB']->label		= 'Solomon Islands';
$config['countries']['SB']->continent	= 'OC';
$config['countries']['SB']->currency	= 'SBD';

$config['countries']['SO']				= new stdClass();
$config['countries']['SO']->code		= 'SO';
$config['countries']['SO']->code_3		= 'SOM';
$config['countries']['SO']->number		= '706';
$config['countries']['SO']->label		= 'Somalia';
$config['countries']['SO']->continent	= 'AF';
$config['countries']['SO']->currency	= 'SOS';

$config['countries']['ZA']				= new stdClass();
$config['countries']['ZA']->code		= 'ZA';
$config['countries']['ZA']->code_3		= 'ZAF';
$config['countries']['ZA']->number		= '710';
$config['countries']['ZA']->label		= 'South Africa';
$config['countries']['ZA']->continent	= 'AF';
$config['countries']['ZA']->currency	= 'ZAR';

$config['countries']['GS']				= new stdClass();
$config['countries']['GS']->code		= 'GS';
$config['countries']['GS']->code_3		= 'SGS';
$config['countries']['GS']->number		= '239';
$config['countries']['GS']->label		= 'South Georgia and the South Sandwich Islands';
$config['countries']['GS']->continent	= 'AN';
$config['countries']['GS']->currency	= 'GBP';

$config['countries']['SS']				= new stdClass();
$config['countries']['SS']->code		= 'SS';
$config['countries']['SS']->code_3		= 'SSD';
$config['countries']['SS']->number		= '728';
$config['countries']['SS']->label		= 'South Sudan';
$config['countries']['SS']->continent	= '';
$config['countries']['SS']->currency	= 'SDG';

$config['countries']['ES']				= new stdClass();
$config['countries']['ES']->code		= 'ES';
$config['countries']['ES']->code_3		= 'ESP';
$config['countries']['ES']->number		= '724';
$config['countries']['ES']->label		= 'Spain';
$config['countries']['ES']->continent	= 'EU';
$config['countries']['ES']->currency	= 'EUR';

$config['countries']['LK']				= new stdClass();
$config['countries']['LK']->code		= 'LK';
$config['countries']['LK']->code_3		= 'LKA';
$config['countries']['LK']->number		= '144';
$config['countries']['LK']->label		= 'Sri Lanka';
$config['countries']['LK']->continent	= 'AS';
$config['countries']['LK']->currency	= 'LKR';

$config['countries']['SD']				= new stdClass();
$config['countries']['SD']->code		= 'SD';
$config['countries']['SD']->code_3		= 'SDN';
$config['countries']['SD']->number		= '736';
$config['countries']['SD']->label		= 'Sudan';
$config['countries']['SD']->continent	= 'AF';
$config['countries']['SD']->currency	= 'SDG';

$config['countries']['SR']				= new stdClass();
$config['countries']['SR']->code		= 'SR';
$config['countries']['SR']->code_3		= 'SUR';
$config['countries']['SR']->number		= '740';
$config['countries']['SR']->label		= 'Suriname';
$config['countries']['SR']->continent	= 'SA';
$config['countries']['SR']->currency	= 'SRD';

$config['countries']['SJ']				= new stdClass();
$config['countries']['SJ']->code		= 'SJ';
$config['countries']['SJ']->code_3		= 'SJM';
$config['countries']['SJ']->number		= '744';
$config['countries']['SJ']->label		= 'Svalbard and Jan Mayen Islands';
$config['countries']['SJ']->continent	= 'EU';
$config['countries']['SJ']->currency	= 'NOK';

$config['countries']['SZ']				= new stdClass();
$config['countries']['SZ']->code		= 'SZ';
$config['countries']['SZ']->code_3		= 'SWZ';
$config['countries']['SZ']->number		= '748';
$config['countries']['SZ']->label		= 'Swaziland';
$config['countries']['SZ']->continent	= 'AF';
$config['countries']['SZ']->currency	= 'SZL';

$config['countries']['SE']				= new stdClass();
$config['countries']['SE']->code		= 'SE';
$config['countries']['SE']->code_3		= 'SWE';
$config['countries']['SE']->number		= '752';
$config['countries']['SE']->label		= 'Sweden';
$config['countries']['SE']->continent	= 'EU';
$config['countries']['SE']->currency	= 'SEK';

$config['countries']['CH']				= new stdClass();
$config['countries']['CH']->code		= 'CH';
$config['countries']['CH']->code_3		= 'CHE';
$config['countries']['CH']->number		= '756';
$config['countries']['CH']->label		= 'Switzerland';
$config['countries']['CH']->continent	= 'EU';
$config['countries']['CH']->currency	= 'CHF';

$config['countries']['SY']				= new stdClass();
$config['countries']['SY']->code		= 'SY';
$config['countries']['SY']->code_3		= 'SYR';
$config['countries']['SY']->number		= '760';
$config['countries']['SY']->label		= 'Syrian Arab Republic';
$config['countries']['SY']->continent	= 'AS';
$config['countries']['SY']->currency	= 'SYP';

$config['countries']['TW']				= new stdClass();
$config['countries']['TW']->code		= 'TW';
$config['countries']['TW']->code_3		= 'TWN';
$config['countries']['TW']->number		= '158';
$config['countries']['TW']->label		= 'Taiwan';
$config['countries']['TW']->continent	= 'AS';
$config['countries']['TW']->currency	= 'TWD';

$config['countries']['TJ']				= new stdClass();
$config['countries']['TJ']->code		= 'TJ';
$config['countries']['TJ']->code_3		= 'TJK';
$config['countries']['TJ']->number		= '762';
$config['countries']['TJ']->label		= 'Tajikistan';
$config['countries']['TJ']->continent	= 'AS';
$config['countries']['TJ']->currency	= 'TJS';

$config['countries']['TZ']				= new stdClass();
$config['countries']['TZ']->code		= 'TZ';
$config['countries']['TZ']->code_3		= 'TZA';
$config['countries']['TZ']->number		= '834';
$config['countries']['TZ']->label		= 'Tanzania, United Republic of';
$config['countries']['TZ']->continent	= 'AF';
$config['countries']['TZ']->currency	= 'TZS';

$config['countries']['TH']				= new stdClass();
$config['countries']['TH']->code		= 'TH';
$config['countries']['TH']->code_3		= 'THA';
$config['countries']['TH']->number		= '764';
$config['countries']['TH']->label		= 'Thailand';
$config['countries']['TH']->continent	= 'AS';
$config['countries']['TH']->currency	= 'THB';

$config['countries']['TL']				= new stdClass();
$config['countries']['TL']->code		= 'TL';
$config['countries']['TL']->code_3		= 'TLS';
$config['countries']['TL']->number		= '626';
$config['countries']['TL']->label		= 'Timor-Leste';
$config['countries']['TL']->continent	= 'AS';
$config['countries']['TL']->currency	= 'USD';

$config['countries']['TG']				= new stdClass();
$config['countries']['TG']->code		= 'TG';
$config['countries']['TG']->code_3		= 'TGO';
$config['countries']['TG']->number		= '768';
$config['countries']['TG']->label		= 'Togo';
$config['countries']['TG']->continent	= 'AF';
$config['countries']['TG']->currency	= 'XOF';

$config['countries']['TK']				= new stdClass();
$config['countries']['TK']->code		= 'TK';
$config['countries']['TK']->code_3		= 'TKL';
$config['countries']['TK']->number		= '772';
$config['countries']['TK']->label		= 'Tokelau';
$config['countries']['TK']->continent	= 'OC';
$config['countries']['TK']->currency	= 'NZD';

$config['countries']['TO']				= new stdClass();
$config['countries']['TO']->code		= 'TO';
$config['countries']['TO']->code_3		= 'TON';
$config['countries']['TO']->number		= '776';
$config['countries']['TO']->label		= 'Tonga';
$config['countries']['TO']->continent	= 'OC';
$config['countries']['TO']->currency	= 'TOP';

$config['countries']['TT']				= new stdClass();
$config['countries']['TT']->code		= 'TT';
$config['countries']['TT']->code_3		= 'TTO';
$config['countries']['TT']->number		= '780';
$config['countries']['TT']->label		= 'Trinidad and Tobago';
$config['countries']['TT']->continent	= 'NA';
$config['countries']['TT']->currency	= 'TTD';

$config['countries']['TN']				= new stdClass();
$config['countries']['TN']->code		= 'TN';
$config['countries']['TN']->code_3		= 'TUN';
$config['countries']['TN']->number		= '788';
$config['countries']['TN']->label		= 'Tunisia';
$config['countries']['TN']->continent	= 'AF';
$config['countries']['TN']->currency	= 'TND';

$config['countries']['TR']				= new stdClass();
$config['countries']['TR']->code		= 'TR';
$config['countries']['TR']->code_3		= 'TUR';
$config['countries']['TR']->number		= '792';
$config['countries']['TR']->label		= 'Turkey';
$config['countries']['TR']->continent	= 'EU';
$config['countries']['TR']->currency	= 'TRY';

$config['countries']['TM']				= new stdClass();
$config['countries']['TM']->code		= 'TM';
$config['countries']['TM']->code_3		= 'TKM';
$config['countries']['TM']->number		= '795';
$config['countries']['TM']->label		= 'Turkmenistan';
$config['countries']['TM']->continent	= 'AS';
$config['countries']['TM']->currency	= 'TMT';

$config['countries']['TC']				= new stdClass();
$config['countries']['TC']->code		= 'TC';
$config['countries']['TC']->code_3		= 'TCA';
$config['countries']['TC']->number		= '796';
$config['countries']['TC']->label		= 'Turks and Caicos Islands';
$config['countries']['TC']->continent	= 'NA';
$config['countries']['TC']->currency	= 'USD';

$config['countries']['UG']				= new stdClass();
$config['countries']['UG']->code		= 'UG';
$config['countries']['UG']->code_3		= 'UGA';
$config['countries']['UG']->number		= '800';
$config['countries']['UG']->label		= 'Uganda';
$config['countries']['UG']->continent	= 'AF';
$config['countries']['UG']->currency	= 'UGX';

$config['countries']['UA']				= new stdClass();
$config['countries']['UA']->code		= 'UA';
$config['countries']['UA']->code_3		= 'UKR';
$config['countries']['UA']->number		= '804';
$config['countries']['UA']->label		= 'Ukraine';
$config['countries']['UA']->continent	= 'EU';
$config['countries']['UA']->currency	= 'UAH';

$config['countries']['AE']				= new stdClass();
$config['countries']['AE']->code		= 'AE';
$config['countries']['AE']->code_3		= 'ARE';
$config['countries']['AE']->number		= '784';
$config['countries']['AE']->label		= 'United Arab Emirates';
$config['countries']['AE']->continent	= 'AS';
$config['countries']['AE']->currency	= 'AED';

$config['countries']['UM']				= new stdClass();
$config['countries']['UM']->code		= 'UM';
$config['countries']['UM']->code_3		= 'UMI';
$config['countries']['UM']->number		= '581';
$config['countries']['UM']->label		= 'United States Minor Outlying Islands';
$config['countries']['UM']->continent	= 'OC';
$config['countries']['UM']->currency	= 'USD';

$config['countries']['UY']				= new stdClass();
$config['countries']['UY']->code		= 'UY';
$config['countries']['UY']->code_3		= 'URY';
$config['countries']['UY']->number		= '858';
$config['countries']['UY']->label		= 'Uruguay';
$config['countries']['UY']->continent	= 'SA';
$config['countries']['UY']->currency	= 'UYU';

$config['countries']['UZ']				= new stdClass();
$config['countries']['UZ']->code		= 'UZ';
$config['countries']['UZ']->code_3		= 'UZB';
$config['countries']['UZ']->number		= '860';
$config['countries']['UZ']->label		= 'Uzbekistan';
$config['countries']['UZ']->continent	= 'AS';
$config['countries']['UZ']->currency	= 'UZS';

$config['countries']['VU']				= new stdClass();
$config['countries']['VU']->code		= 'VU';
$config['countries']['VU']->code_3		= 'VUT';
$config['countries']['VU']->number		= '548';
$config['countries']['VU']->label		= 'Vanuatu';
$config['countries']['VU']->continent	= 'OC';
$config['countries']['VU']->currency	= 'VUV';

$config['countries']['VE']				= new stdClass();
$config['countries']['VE']->code		= 'VE';
$config['countries']['VE']->code_3		= 'VEN';
$config['countries']['VE']->number		= '862';
$config['countries']['VE']->label		= 'Venezuela';
$config['countries']['VE']->continent	= 'SA';
$config['countries']['VE']->currency	= 'VEF';

$config['countries']['VN']				= new stdClass();
$config['countries']['VN']->code		= 'VN';
$config['countries']['VN']->code_3		= 'VNM';
$config['countries']['VN']->number		= '704';
$config['countries']['VN']->label		= 'Viet Nam';
$config['countries']['VN']->continent	= 'AS';
$config['countries']['VN']->currency	= 'VND';

$config['countries']['VG']				= new stdClass();
$config['countries']['VG']->code		= 'VG';
$config['countries']['VG']->code_3		= 'VGB';
$config['countries']['VG']->number		= '092';
$config['countries']['VG']->label		= 'Virgin Islands (British)';
$config['countries']['VG']->continent	= 'NA';
$config['countries']['VG']->currency	= 'USD';

$config['countries']['VI']				= new stdClass();
$config['countries']['VI']->code		= 'VI';
$config['countries']['VI']->code_3		= 'VIR';
$config['countries']['VI']->number		= '850';
$config['countries']['VI']->label		= 'Virgin Islands (U.S.)';
$config['countries']['VI']->continent	= 'NA';
$config['countries']['VI']->currency	= 'USD';

$config['countries']['WF']				= new stdClass();
$config['countries']['WF']->code		= 'WF';
$config['countries']['WF']->code_3		= 'WLF';
$config['countries']['WF']->number		= '876';
$config['countries']['WF']->label		= 'Wallis and Futuna Islands';
$config['countries']['WF']->continent	= 'OC';
$config['countries']['WF']->currency	= 'XPF';

$config['countries']['EH']				= new stdClass();
$config['countries']['EH']->code		= 'EH';
$config['countries']['EH']->code_3		= 'ESH';
$config['countries']['EH']->number		= '732';
$config['countries']['EH']->label		= 'Western Sahara';
$config['countries']['EH']->continent	= 'AF';
$config['countries']['EH']->currency	= 'MAD';

$config['countries']['YE']				= new stdClass();
$config['countries']['YE']->code		= 'YE';
$config['countries']['YE']->code_3		= 'YEM';
$config['countries']['YE']->number		= '887';
$config['countries']['YE']->label		= 'Yemen';
$config['countries']['YE']->continent	= 'AS';
$config['countries']['YE']->currency	= 'YER';

$config['countries']['ZM']				= new stdClass();
$config['countries']['ZM']->code		= 'ZM';
$config['countries']['ZM']->code_3		= 'ZMB';
$config['countries']['ZM']->number		= '894';
$config['countries']['ZM']->label		= 'Zambia';
$config['countries']['ZM']->continent	= 'AF';
$config['countries']['ZM']->currency	= 'ZMK';

$config['countries']['ZW']				= new stdClass();
$config['countries']['ZW']->code		= 'ZW';
$config['countries']['ZW']->code_3		= 'ZWE';
$config['countries']['ZW']->number		= '716';
$config['countries']['ZW']->label		= 'Zimbabwe';
$config['countries']['ZW']->continent	= 'AF';
$config['countries']['ZW']->currency	= 'ZWL';


/* End of file countries.php */
/* Location: ./config/countries.php */