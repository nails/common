<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| CURRENCIES
|--------------------------------------------------------------------------
|
| This config file contains a list of current world currencies.
|
*/

$config['currency'] = array();

//	Special treatment :>
$config['currency']['GBP']						= new stdClass();
$config['currency']['GBP']->code				= 'GBP';
$config['currency']['GBP']->symbol				= '&pound;';
$config['currency']['GBP']->symbol_position		= 'BEFORE';
$config['currency']['GBP']->label				= 'British Pound Sterling';
$config['currency']['GBP']->decimal_precision	= 2;
$config['currency']['GBP']->decimal_symbol		= '.';
$config['currency']['GBP']->thousands_seperator	= ',';

$config['currency']['USD']						= new stdClass();
$config['currency']['USD']->code				= 'USD';
$config['currency']['USD']->symbol				= '&dollar;';
$config['currency']['USD']->symbol_position		= 'BEFORE';
$config['currency']['USD']->label				= 'United States Dollar';
$config['currency']['USD']->decimal_precision	= 2;
$config['currency']['USD']->decimal_symbol		= '.';
$config['currency']['USD']->thousands_seperator	= ',';

$config['currency']['EUR']						= new stdClass();
$config['currency']['EUR']->code				= 'EUR';
$config['currency']['EUR']->symbol				= '&euro;';
$config['currency']['EUR']->symbol_position		= 'BEFORE';
$config['currency']['EUR']->label				= 'Euro';
$config['currency']['EUR']->decimal_precision	= 2;
$config['currency']['EUR']->decimal_symbol		= '.';
$config['currency']['EUR']->thousands_seperator	= ',';

// --------------------------------------------------------------------------

$config['currency']['AFN']						= new stdClass();
$config['currency']['AFN']->code				= 'AFN';
$config['currency']['AFN']->symbol				= '&curren;';
$config['currency']['AFN']->symbol_position		= 'BEFORE';
$config['currency']['AFN']->label				= 'Afghan Afghani';
$config['currency']['AFN']->decimal_precision	= 2;
$config['currency']['AFN']->decimal_symbol		= '.';
$config['currency']['AFN']->thousands_seperator	= ',';

$config['currency']['ALL']						= new stdClass();
$config['currency']['ALL']->code				= 'ALL';
$config['currency']['ALL']->symbol				= '&curren;';
$config['currency']['ALL']->symbol_position		= 'BEFORE';
$config['currency']['ALL']->label				= 'Albanian Lek';
$config['currency']['ALL']->decimal_precision	= 2;
$config['currency']['ALL']->decimal_symbol		= '.';
$config['currency']['ALL']->thousands_seperator	= ',';

$config['currency']['DZD']						= new stdClass();
$config['currency']['DZD']->code				= 'DZD';
$config['currency']['DZD']->symbol				= '&curren;';
$config['currency']['DZD']->symbol_position		= 'BEFORE';
$config['currency']['DZD']->label				= 'Algerian Dinar';
$config['currency']['DZD']->decimal_precision	= 2;
$config['currency']['DZD']->decimal_symbol		= '.';
$config['currency']['DZD']->thousands_seperator	= ',';

$config['currency']['AOA']						= new stdClass();
$config['currency']['AOA']->code				= 'AOA';
$config['currency']['AOA']->symbol				= '&curren;';
$config['currency']['AOA']->symbol_position		= 'BEFORE';
$config['currency']['AOA']->label				= 'Angolan Kwanza';
$config['currency']['AOA']->decimal_precision	= 2;
$config['currency']['AOA']->decimal_symbol		= '.';
$config['currency']['AOA']->thousands_seperator	= ',';

$config['currency']['ARS']						= new stdClass();
$config['currency']['ARS']->code				= 'ARS';
$config['currency']['ARS']->symbol				= '&curren;';
$config['currency']['ARS']->symbol_position		= 'BEFORE';
$config['currency']['ARS']->label				= 'Argentine Peso';
$config['currency']['ARS']->decimal_precision	= 2;
$config['currency']['ARS']->decimal_symbol		= '.';
$config['currency']['ARS']->thousands_seperator	= ',';

$config['currency']['AMD']						= new stdClass();
$config['currency']['AMD']->code				= 'AMD';
$config['currency']['AMD']->symbol				= '&curren;';
$config['currency']['AMD']->symbol_position		= 'BEFORE';
$config['currency']['AMD']->label				= 'Armenian Dram';
$config['currency']['AMD']->decimal_precision	= 2;
$config['currency']['AMD']->decimal_symbol		= '.';
$config['currency']['AMD']->thousands_seperator	= ',';

$config['currency']['AWG']						= new stdClass();
$config['currency']['AWG']->code				= 'AWG';
$config['currency']['AWG']->symbol				= '&curren;';
$config['currency']['AWG']->symbol_position		= 'BEFORE';
$config['currency']['AWG']->label				= 'Aruban Florin';
$config['currency']['AWG']->decimal_precision	= 2;
$config['currency']['AWG']->decimal_symbol		= '.';
$config['currency']['AWG']->thousands_seperator	= ',';

$config['currency']['AUD']						= new stdClass();
$config['currency']['AUD']->code				= 'AUD';
$config['currency']['AUD']->symbol				= '&curren;';
$config['currency']['AUD']->symbol_position		= 'BEFORE';
$config['currency']['AUD']->label				= 'Australian Dollar';
$config['currency']['AUD']->decimal_precision	= 2;
$config['currency']['AUD']->decimal_symbol		= '.';
$config['currency']['AUD']->thousands_seperator	= ',';

$config['currency']['AZN']						= new stdClass();
$config['currency']['AZN']->code				= 'AZN';
$config['currency']['AZN']->symbol				= '&curren;';
$config['currency']['AZN']->symbol_position		= 'BEFORE';
$config['currency']['AZN']->label				= 'Azerbaijani Manat';
$config['currency']['AZN']->decimal_precision	= 2;
$config['currency']['AZN']->decimal_symbol		= '.';
$config['currency']['AZN']->thousands_seperator	= ',';

$config['currency']['BSD']						= new stdClass();
$config['currency']['BSD']->code				= 'BSD';
$config['currency']['BSD']->symbol				= '&curren;';
$config['currency']['BSD']->symbol_position		= 'BEFORE';
$config['currency']['BSD']->label				= 'Bahamian Dollar';
$config['currency']['BSD']->decimal_precision	= 2;
$config['currency']['BSD']->decimal_symbol		= '.';
$config['currency']['BSD']->thousands_seperator	= ',';

$config['currency']['BHD']						= new stdClass();
$config['currency']['BHD']->code				= 'BHD';
$config['currency']['BHD']->symbol				= '&curren;';
$config['currency']['BHD']->symbol_position		= 'BEFORE';
$config['currency']['BHD']->label				= 'Bahraini Dinar';
$config['currency']['BHD']->decimal_precision	= 2;
$config['currency']['BHD']->decimal_symbol		= '.';
$config['currency']['BHD']->thousands_seperator	= ',';

$config['currency']['BDT']						= new stdClass();
$config['currency']['BDT']->code				= 'BDT';
$config['currency']['BDT']->symbol				= '&curren;';
$config['currency']['BDT']->symbol_position		= 'BEFORE';
$config['currency']['BDT']->label				= 'Bangladeshi Taka';
$config['currency']['BDT']->decimal_precision	= 2;
$config['currency']['BDT']->decimal_symbol		= '.';
$config['currency']['BDT']->thousands_seperator	= ',';

$config['currency']['BBD']						= new stdClass();
$config['currency']['BBD']->code				= 'BBD';
$config['currency']['BBD']->symbol				= '&curren;';
$config['currency']['BBD']->symbol_position		= 'BEFORE';
$config['currency']['BBD']->label				= 'Barbadian Dollar';
$config['currency']['BBD']->decimal_precision	= 2;
$config['currency']['BBD']->decimal_symbol		= '.';
$config['currency']['BBD']->thousands_seperator	= ',';

$config['currency']['BYR']						= new stdClass();
$config['currency']['BYR']->code				= 'BYR';
$config['currency']['BYR']->symbol				= '&curren;';
$config['currency']['BYR']->symbol_position		= 'BEFORE';
$config['currency']['BYR']->label				= 'Belarusian Ruble';
$config['currency']['BYR']->decimal_precision	= 2;
$config['currency']['BYR']->decimal_symbol		= '.';
$config['currency']['BYR']->thousands_seperator	= ',';

$config['currency']['BZD']						= new stdClass();
$config['currency']['BZD']->code				= 'BZD';
$config['currency']['BZD']->symbol				= '&curren;';
$config['currency']['BZD']->symbol_position		= 'BEFORE';
$config['currency']['BZD']->label				= 'Belize Dollar';
$config['currency']['BZD']->decimal_precision	= 2;
$config['currency']['BZD']->decimal_symbol		= '.';
$config['currency']['BZD']->thousands_seperator	= ',';

$config['currency']['BMD']						= new stdClass();
$config['currency']['BMD']->code				= 'BMD';
$config['currency']['BMD']->symbol				= '&curren;';
$config['currency']['BMD']->symbol_position		= 'BEFORE';
$config['currency']['BMD']->label				= 'Bermudan Dollar';
$config['currency']['BMD']->decimal_precision	= 2;
$config['currency']['BMD']->decimal_symbol		= '.';
$config['currency']['BMD']->thousands_seperator	= ',';

$config['currency']['BTN']						= new stdClass();
$config['currency']['BTN']->code				= 'BTN';
$config['currency']['BTN']->symbol				= '&curren;';
$config['currency']['BTN']->symbol_position		= 'BEFORE';
$config['currency']['BTN']->label				= 'Bhutanese Ngultrum';
$config['currency']['BTN']->decimal_precision	= 2;
$config['currency']['BTN']->decimal_symbol		= '.';
$config['currency']['BTN']->thousands_seperator	= ',';

$config['currency']['BTC']						= new stdClass();
$config['currency']['BTC']->code				= 'BTC';
$config['currency']['BTC']->symbol				= '&curren;';
$config['currency']['BTC']->symbol_position		= 'BEFORE';
$config['currency']['BTC']->label				= 'Bitcoin';
$config['currency']['BTC']->decimal_precision	= 2;
$config['currency']['BTC']->decimal_symbol		= '.';
$config['currency']['BTC']->thousands_seperator	= ',';

$config['currency']['BOB']						= new stdClass();
$config['currency']['BOB']->code				= 'BOB';
$config['currency']['BOB']->symbol				= '&curren;';
$config['currency']['BOB']->symbol_position		= 'BEFORE';
$config['currency']['BOB']->label				= 'Bolivian Boliviano';
$config['currency']['BOB']->decimal_precision	= 2;
$config['currency']['BOB']->decimal_symbol		= '.';
$config['currency']['BOB']->thousands_seperator	= ',';

$config['currency']['BAM']						= new stdClass();
$config['currency']['BAM']->code				= 'BAM';
$config['currency']['BAM']->symbol				= '&curren;';
$config['currency']['BAM']->symbol_position		= 'BEFORE';
$config['currency']['BAM']->label				= 'Bosnia-Herzegovina Convertible Mark';
$config['currency']['BAM']->decimal_precision	= 2;
$config['currency']['BAM']->decimal_symbol		= '.';
$config['currency']['BAM']->thousands_seperator	= ',';

$config['currency']['BWP']						= new stdClass();
$config['currency']['BWP']->code				= 'BWP';
$config['currency']['BWP']->symbol				= '&curren;';
$config['currency']['BWP']->symbol_position		= 'BEFORE';
$config['currency']['BWP']->label				= 'Botswanan Pula';
$config['currency']['BWP']->decimal_precision	= 2;
$config['currency']['BWP']->decimal_symbol		= '.';
$config['currency']['BWP']->thousands_seperator	= ',';

$config['currency']['BRL']						= new stdClass();
$config['currency']['BRL']->code				= 'BRL';
$config['currency']['BRL']->symbol				= '&curren;';
$config['currency']['BRL']->symbol_position		= 'BEFORE';
$config['currency']['BRL']->label				= 'Brazilian Real';
$config['currency']['BRL']->decimal_precision	= 2;
$config['currency']['BRL']->decimal_symbol		= '.';
$config['currency']['BRL']->thousands_seperator	= ',';

$config['currency']['BND']						= new stdClass();
$config['currency']['BND']->code				= 'BND';
$config['currency']['BND']->symbol				= '&curren;';
$config['currency']['BND']->symbol_position		= 'BEFORE';
$config['currency']['BND']->label				= 'Brunei Dollar';
$config['currency']['BND']->decimal_precision	= 2;
$config['currency']['BND']->decimal_symbol		= '.';
$config['currency']['BND']->thousands_seperator	= ',';

$config['currency']['BGN']						= new stdClass();
$config['currency']['BGN']->code				= 'BGN';
$config['currency']['BGN']->symbol				= '&curren;';
$config['currency']['BGN']->symbol_position		= 'BEFORE';
$config['currency']['BGN']->label				= 'Bulgarian Lev';
$config['currency']['BGN']->decimal_precision	= 2;
$config['currency']['BGN']->decimal_symbol		= '.';
$config['currency']['BGN']->thousands_seperator	= ',';

$config['currency']['MMK']						= new stdClass();
$config['currency']['MMK']->code				= 'MMK';
$config['currency']['MMK']->symbol				= '&curren;';
$config['currency']['MMK']->symbol_position		= 'BEFORE';
$config['currency']['MMK']->label				= 'Burmese Kyat';
$config['currency']['MMK']->decimal_precision	= 2;
$config['currency']['MMK']->decimal_symbol		= '.';
$config['currency']['MMK']->thousands_seperator	= ',';

$config['currency']['BIF']						= new stdClass();
$config['currency']['BIF']->code				= 'BIF';
$config['currency']['BIF']->symbol				= '&curren;';
$config['currency']['BIF']->symbol_position		= 'BEFORE';
$config['currency']['BIF']->label				= 'Burundian Franc';
$config['currency']['BIF']->decimal_precision	= 2;
$config['currency']['BIF']->decimal_symbol		= '.';
$config['currency']['BIF']->thousands_seperator	= ',';

$config['currency']['XOF']						= new stdClass();
$config['currency']['XOF']->code				= 'XOF';
$config['currency']['XOF']->symbol				= '&curren;';
$config['currency']['XOF']->symbol_position		= 'BEFORE';
$config['currency']['XOF']->label				= 'CFA Franc BCEAO';
$config['currency']['XOF']->decimal_precision	= 2;
$config['currency']['XOF']->decimal_symbol		= '.';
$config['currency']['XOF']->thousands_seperator	= ',';

$config['currency']['XAF']						= new stdClass();
$config['currency']['XAF']->code				= 'XAF';
$config['currency']['XAF']->symbol				= '&curren;';
$config['currency']['XAF']->symbol_position		= 'BEFORE';
$config['currency']['XAF']->label				= 'CFA Franc BEAC';
$config['currency']['XAF']->decimal_precision	= 2;
$config['currency']['XAF']->decimal_symbol		= '.';
$config['currency']['XAF']->thousands_seperator	= ',';

$config['currency']['XPF']						= new stdClass();
$config['currency']['XPF']->code				= 'XPF';
$config['currency']['XPF']->symbol				= '&curren;';
$config['currency']['XPF']->symbol_position		= 'BEFORE';
$config['currency']['XPF']->label				= 'CFP Franc';
$config['currency']['XPF']->decimal_precision	= 2;
$config['currency']['XPF']->decimal_symbol		= '.';
$config['currency']['XPF']->thousands_seperator	= ',';

$config['currency']['KHR']						= new stdClass();
$config['currency']['KHR']->code				= 'KHR';
$config['currency']['KHR']->symbol				= '&curren;';
$config['currency']['KHR']->symbol_position		= 'BEFORE';
$config['currency']['KHR']->label				= 'Cambodian Riel';
$config['currency']['KHR']->decimal_precision	= 2;
$config['currency']['KHR']->decimal_symbol		= '.';
$config['currency']['KHR']->thousands_seperator	= ',';

$config['currency']['CAD']						= new stdClass();
$config['currency']['CAD']->code				= 'CAD';
$config['currency']['CAD']->symbol				= '&curren;';
$config['currency']['CAD']->symbol_position		= 'BEFORE';
$config['currency']['CAD']->label				= 'Canadian Dollar';
$config['currency']['CAD']->decimal_precision	= 2;
$config['currency']['CAD']->decimal_symbol		= '.';
$config['currency']['CAD']->thousands_seperator	= ',';

$config['currency']['CVE']						= new stdClass();
$config['currency']['CVE']->code				= 'CVE';
$config['currency']['CVE']->symbol				= '&curren;';
$config['currency']['CVE']->symbol_position		= 'BEFORE';
$config['currency']['CVE']->label				= 'Cape Verdean Escudo';
$config['currency']['CVE']->decimal_precision	= 2;
$config['currency']['CVE']->decimal_symbol		= '.';
$config['currency']['CVE']->thousands_seperator	= ',';

$config['currency']['KYD']						= new stdClass();
$config['currency']['KYD']->code				= 'KYD';
$config['currency']['KYD']->symbol				= '&curren;';
$config['currency']['KYD']->symbol_position		= 'BEFORE';
$config['currency']['KYD']->label				= 'Cayman Islands Dollar';
$config['currency']['KYD']->decimal_precision	= 2;
$config['currency']['KYD']->decimal_symbol		= '.';
$config['currency']['KYD']->thousands_seperator	= ',';

$config['currency']['CLP']						= new stdClass();
$config['currency']['CLP']->code				= 'CLP';
$config['currency']['CLP']->symbol				= '&curren;';
$config['currency']['CLP']->symbol_position		= 'BEFORE';
$config['currency']['CLP']->label				= 'Chilean Peso';
$config['currency']['CLP']->decimal_precision	= 2;
$config['currency']['CLP']->decimal_symbol		= '.';
$config['currency']['CLP']->thousands_seperator	= ',';

$config['currency']['CLF']						= new stdClass();
$config['currency']['CLF']->code				= 'CLF';
$config['currency']['CLF']->symbol				= '&curren;';
$config['currency']['CLF']->symbol_position		= 'BEFORE';
$config['currency']['CLF']->label				= 'Chilean Unit of Account (UF)';
$config['currency']['CLF']->decimal_precision	= 2;
$config['currency']['CLF']->decimal_symbol		= '.';
$config['currency']['CLF']->thousands_seperator	= ',';

$config['currency']['CNY']						= new stdClass();
$config['currency']['CNY']->code				= 'CNY';
$config['currency']['CNY']->symbol				= '&curren;';
$config['currency']['CNY']->symbol_position		= 'BEFORE';
$config['currency']['CNY']->label				= 'Chinese Yuan';
$config['currency']['CNY']->decimal_precision	= 2;
$config['currency']['CNY']->decimal_symbol		= '.';
$config['currency']['CNY']->thousands_seperator	= ',';

$config['currency']['COP']						= new stdClass();
$config['currency']['COP']->code				= 'COP';
$config['currency']['COP']->symbol				= '&curren;';
$config['currency']['COP']->symbol_position		= 'BEFORE';
$config['currency']['COP']->label				= 'Colombian Peso';
$config['currency']['COP']->decimal_precision	= 2;
$config['currency']['COP']->decimal_symbol		= '.';
$config['currency']['COP']->thousands_seperator	= ',';

$config['currency']['KMF']						= new stdClass();
$config['currency']['KMF']->code				= 'KMF';
$config['currency']['KMF']->symbol				= '&curren;';
$config['currency']['KMF']->symbol_position		= 'BEFORE';
$config['currency']['KMF']->label				= 'Comorian Franc';
$config['currency']['KMF']->decimal_precision	= 2;
$config['currency']['KMF']->decimal_symbol		= '.';
$config['currency']['KMF']->thousands_seperator	= ',';

$config['currency']['CDF']						= new stdClass();
$config['currency']['CDF']->code				= 'CDF';
$config['currency']['CDF']->symbol				= '&curren;';
$config['currency']['CDF']->symbol_position		= 'BEFORE';
$config['currency']['CDF']->label				= 'Congolese Franc';
$config['currency']['CDF']->decimal_precision	= 2;
$config['currency']['CDF']->decimal_symbol		= '.';
$config['currency']['CDF']->thousands_seperator	= ',';

$config['currency']['CRC']						= new stdClass();
$config['currency']['CRC']->code				= 'CRC';
$config['currency']['CRC']->symbol				= '&curren;';
$config['currency']['CRC']->symbol_position		= 'BEFORE';
$config['currency']['CRC']->label				= 'Costa Rican Colón';
$config['currency']['CRC']->decimal_precision	= 2;
$config['currency']['CRC']->decimal_symbol		= '.';
$config['currency']['CRC']->thousands_seperator	= ',';

$config['currency']['HRK']						= new stdClass();
$config['currency']['HRK']->code				= 'HRK';
$config['currency']['HRK']->symbol				= '&curren;';
$config['currency']['HRK']->symbol_position		= 'BEFORE';
$config['currency']['HRK']->label				= 'Croatian Kuna';
$config['currency']['HRK']->decimal_precision	= 2;
$config['currency']['HRK']->decimal_symbol		= '.';
$config['currency']['HRK']->thousands_seperator	= ',';

$config['currency']['CUP']						= new stdClass();
$config['currency']['CUP']->code				= 'CUP';
$config['currency']['CUP']->symbol				= '&curren;';
$config['currency']['CUP']->symbol_position		= 'BEFORE';
$config['currency']['CUP']->label				= 'Cuban Peso';
$config['currency']['CUP']->decimal_precision	= 2;
$config['currency']['CUP']->decimal_symbol		= '.';
$config['currency']['CUP']->thousands_seperator	= ',';

$config['currency']['CZK']						= new stdClass();
$config['currency']['CZK']->code				= 'CZK';
$config['currency']['CZK']->symbol				= '&curren;';
$config['currency']['CZK']->symbol_position		= 'BEFORE';
$config['currency']['CZK']->label				= 'Czech Republic Koruna';
$config['currency']['CZK']->decimal_precision	= 2;
$config['currency']['CZK']->decimal_symbol		= '.';
$config['currency']['CZK']->thousands_seperator	= ',';

$config['currency']['DKK']						= new stdClass();
$config['currency']['DKK']->code				= 'DKK';
$config['currency']['DKK']->symbol				= '&curren;';
$config['currency']['DKK']->symbol_position		= 'BEFORE';
$config['currency']['DKK']->label				= 'Danish Krone';
$config['currency']['DKK']->decimal_precision	= 2;
$config['currency']['DKK']->decimal_symbol		= '.';
$config['currency']['DKK']->thousands_seperator	= ',';

$config['currency']['DJF']						= new stdClass();
$config['currency']['DJF']->code				= 'DJF';
$config['currency']['DJF']->symbol				= '&curren;';
$config['currency']['DJF']->symbol_position		= 'BEFORE';
$config['currency']['DJF']->label				= 'Djiboutian Franc';
$config['currency']['DJF']->decimal_precision	= 2;
$config['currency']['DJF']->decimal_symbol		= '.';
$config['currency']['DJF']->thousands_seperator	= ',';

$config['currency']['DOP']						= new stdClass();
$config['currency']['DOP']->code				= 'DOP';
$config['currency']['DOP']->symbol				= '&curren;';
$config['currency']['DOP']->symbol_position		= 'BEFORE';
$config['currency']['DOP']->label				= 'Dominican Peso';
$config['currency']['DOP']->decimal_precision	= 2;
$config['currency']['DOP']->decimal_symbol		= '.';
$config['currency']['DOP']->thousands_seperator	= ',';

$config['currency']['XCD']						= new stdClass();
$config['currency']['XCD']->code				= 'XCD';
$config['currency']['XCD']->symbol				= '&curren;';
$config['currency']['XCD']->symbol_position		= 'BEFORE';
$config['currency']['XCD']->label				= 'East Caribbean Dollar';
$config['currency']['XCD']->decimal_precision	= 2;
$config['currency']['XCD']->decimal_symbol		= '.';
$config['currency']['XCD']->thousands_seperator	= ',';

$config['currency']['EGP']						= new stdClass();
$config['currency']['EGP']->code				= 'EGP';
$config['currency']['EGP']->symbol				= '&curren;';
$config['currency']['EGP']->symbol_position		= 'BEFORE';
$config['currency']['EGP']->label				= 'Egyptian Pound';
$config['currency']['EGP']->decimal_precision	= 2;
$config['currency']['EGP']->decimal_symbol		= '.';
$config['currency']['EGP']->thousands_seperator	= ',';

$config['currency']['EEK']						= new stdClass();
$config['currency']['EEK']->code				= 'EEK';
$config['currency']['EEK']->symbol				= '&curren;';
$config['currency']['EEK']->symbol_position		= 'BEFORE';
$config['currency']['EEK']->label				= 'Estonian Kroon';
$config['currency']['EEK']->decimal_precision	= 2;
$config['currency']['EEK']->decimal_symbol		= '.';
$config['currency']['EEK']->thousands_seperator	= ',';

$config['currency']['ETB']						= new stdClass();
$config['currency']['ETB']->code				= 'ETB';
$config['currency']['ETB']->symbol				= '&curren;';
$config['currency']['ETB']->symbol_position		= 'BEFORE';
$config['currency']['ETB']->label				= 'Ethiopian Birr';
$config['currency']['ETB']->decimal_precision	= 2;
$config['currency']['ETB']->decimal_symbol		= '.';
$config['currency']['ETB']->thousands_seperator	= ',';

$config['currency']['FKP']						= new stdClass();
$config['currency']['FKP']->code				= 'FKP';
$config['currency']['FKP']->symbol				= '&curren;';
$config['currency']['FKP']->symbol_position		= 'BEFORE';
$config['currency']['FKP']->label				= 'Falkland Islands Pound';
$config['currency']['FKP']->decimal_precision	= 2;
$config['currency']['FKP']->decimal_symbol		= '.';
$config['currency']['FKP']->thousands_seperator	= ',';

$config['currency']['FJD']						= new stdClass();
$config['currency']['FJD']->code				= 'FJD';
$config['currency']['FJD']->symbol				= '&curren;';
$config['currency']['FJD']->symbol_position		= 'BEFORE';
$config['currency']['FJD']->label				= 'Fijian Dollar';
$config['currency']['FJD']->decimal_precision	= 2;
$config['currency']['FJD']->decimal_symbol		= '.';
$config['currency']['FJD']->thousands_seperator	= ',';

$config['currency']['GMD']						= new stdClass();
$config['currency']['GMD']->code				= 'GMD';
$config['currency']['GMD']->symbol				= '&curren;';
$config['currency']['GMD']->symbol_position		= 'BEFORE';
$config['currency']['GMD']->label				= 'Gambian Dalasi';
$config['currency']['GMD']->decimal_precision	= 2;
$config['currency']['GMD']->decimal_symbol		= '.';
$config['currency']['GMD']->thousands_seperator	= ',';

$config['currency']['GEL']						= new stdClass();
$config['currency']['GEL']->code				= 'GEL';
$config['currency']['GEL']->symbol				= '&curren;';
$config['currency']['GEL']->symbol_position		= 'BEFORE';
$config['currency']['GEL']->label				= 'Georgian Lari';
$config['currency']['GEL']->decimal_precision	= 2;
$config['currency']['GEL']->decimal_symbol		= '.';
$config['currency']['GEL']->thousands_seperator	= ',';

$config['currency']['GHS']						= new stdClass();
$config['currency']['GHS']->code				= 'GHS';
$config['currency']['GHS']->symbol				= '&curren;';
$config['currency']['GHS']->symbol_position		= 'BEFORE';
$config['currency']['GHS']->label				= 'Ghanaian Cedi';
$config['currency']['GHS']->decimal_precision	= 2;
$config['currency']['GHS']->decimal_symbol		= '.';
$config['currency']['GHS']->thousands_seperator	= ',';

$config['currency']['GIP']						= new stdClass();
$config['currency']['GIP']->code				= 'GIP';
$config['currency']['GIP']->symbol				= '&curren;';
$config['currency']['GIP']->symbol_position		= 'BEFORE';
$config['currency']['GIP']->label				= 'Gibraltar Pound';
$config['currency']['GIP']->decimal_precision	= 2;
$config['currency']['GIP']->decimal_symbol		= '.';
$config['currency']['GIP']->thousands_seperator	= ',';

$config['currency']['XAU']						= new stdClass();
$config['currency']['XAU']->code				= 'XAU';
$config['currency']['XAU']->symbol				= '&curren;';
$config['currency']['XAU']->symbol_position		= 'BEFORE';
$config['currency']['XAU']->label				= 'Gold (troy ounce)';
$config['currency']['XAU']->decimal_precision	= 2;
$config['currency']['XAU']->decimal_symbol		= '.';
$config['currency']['XAU']->thousands_seperator	= ',';

$config['currency']['GTQ']						= new stdClass();
$config['currency']['GTQ']->code				= 'GTQ';
$config['currency']['GTQ']->symbol				= '&curren;';
$config['currency']['GTQ']->symbol_position		= 'BEFORE';
$config['currency']['GTQ']->label				= 'Guatemalan Quetzal';
$config['currency']['GTQ']->decimal_precision	= 2;
$config['currency']['GTQ']->decimal_symbol		= '.';
$config['currency']['GTQ']->thousands_seperator	= ',';

$config['currency']['GNF']						= new stdClass();
$config['currency']['GNF']->code				= 'GNF';
$config['currency']['GNF']->symbol				= '&curren;';
$config['currency']['GNF']->symbol_position		= 'BEFORE';
$config['currency']['GNF']->label				= 'Guinean Franc';
$config['currency']['GNF']->decimal_precision	= 2;
$config['currency']['GNF']->decimal_symbol		= '.';
$config['currency']['GNF']->thousands_seperator	= ',';

$config['currency']['GYD']						= new stdClass();
$config['currency']['GYD']->code				= 'GYD';
$config['currency']['GYD']->symbol				= '&curren;';
$config['currency']['GYD']->symbol_position		= 'BEFORE';
$config['currency']['GYD']->label				= 'Guyanaese Dollar';
$config['currency']['GYD']->decimal_precision	= 2;
$config['currency']['GYD']->decimal_symbol		= '.';
$config['currency']['GYD']->thousands_seperator	= ',';

$config['currency']['HTG']						= new stdClass();
$config['currency']['HTG']->code				= 'HTG';
$config['currency']['HTG']->symbol				= '&curren;';
$config['currency']['HTG']->symbol_position		= 'BEFORE';
$config['currency']['HTG']->label				= 'Haitian Gourde';
$config['currency']['HTG']->decimal_precision	= 2;
$config['currency']['HTG']->decimal_symbol		= '.';
$config['currency']['HTG']->thousands_seperator	= ',';

$config['currency']['HNL']						= new stdClass();
$config['currency']['HNL']->code				= 'HNL';
$config['currency']['HNL']->symbol				= '&curren;';
$config['currency']['HNL']->symbol_position		= 'BEFORE';
$config['currency']['HNL']->label				= 'Honduran Lempira';
$config['currency']['HNL']->decimal_precision	= 2;
$config['currency']['HNL']->decimal_symbol		= '.';
$config['currency']['HNL']->thousands_seperator	= ',';

$config['currency']['HKD']						= new stdClass();
$config['currency']['HKD']->code				= 'HKD';
$config['currency']['HKD']->symbol				= '&curren;';
$config['currency']['HKD']->symbol_position		= 'BEFORE';
$config['currency']['HKD']->label				= 'Hong Kong Dollar';
$config['currency']['HKD']->decimal_precision	= 2;
$config['currency']['HKD']->decimal_symbol		= '.';
$config['currency']['HKD']->thousands_seperator	= ',';

$config['currency']['HUF']						= new stdClass();
$config['currency']['HUF']->code				= 'HUF';
$config['currency']['HUF']->symbol				= '&curren;';
$config['currency']['HUF']->symbol_position		= 'BEFORE';
$config['currency']['HUF']->label				= 'Hungarian Forint';
$config['currency']['HUF']->decimal_precision	= 2;
$config['currency']['HUF']->decimal_symbol		= '.';
$config['currency']['HUF']->thousands_seperator	= ',';

$config['currency']['ISK']						= new stdClass();
$config['currency']['ISK']->code				= 'ISK';
$config['currency']['ISK']->symbol				= '&curren;';
$config['currency']['ISK']->symbol_position		= 'BEFORE';
$config['currency']['ISK']->label				= 'Icelandic Króna';
$config['currency']['ISK']->decimal_precision	= 2;
$config['currency']['ISK']->decimal_symbol		= '.';
$config['currency']['ISK']->thousands_seperator	= ',';

$config['currency']['INR']						= new stdClass();
$config['currency']['INR']->code				= 'INR';
$config['currency']['INR']->symbol				= '&curren;';
$config['currency']['INR']->symbol_position		= 'BEFORE';
$config['currency']['INR']->label				= 'Indian Rupee';
$config['currency']['INR']->decimal_precision	= 2;
$config['currency']['INR']->decimal_symbol		= '.';
$config['currency']['INR']->thousands_seperator	= ',';

$config['currency']['IDR']						= new stdClass();
$config['currency']['IDR']->code				= 'IDR';
$config['currency']['IDR']->symbol				= '&curren;';
$config['currency']['IDR']->symbol_position		= 'BEFORE';
$config['currency']['IDR']->label				= 'Indonesian Rupiah';
$config['currency']['IDR']->decimal_precision	= 2;
$config['currency']['IDR']->decimal_symbol		= '.';
$config['currency']['IDR']->thousands_seperator	= ',';

$config['currency']['IRR']						= new stdClass();
$config['currency']['IRR']->code				= 'IRR';
$config['currency']['IRR']->symbol				= '&curren;';
$config['currency']['IRR']->symbol_position		= 'BEFORE';
$config['currency']['IRR']->label				= 'Iranian Rial';
$config['currency']['IRR']->decimal_precision	= 2;
$config['currency']['IRR']->decimal_symbol		= '.';
$config['currency']['IRR']->thousands_seperator	= ',';

$config['currency']['IQD']						= new stdClass();
$config['currency']['IQD']->code				= 'IQD';
$config['currency']['IQD']->symbol				= '&curren;';
$config['currency']['IQD']->symbol_position		= 'BEFORE';
$config['currency']['IQD']->label				= 'Iraqi Dinar';
$config['currency']['IQD']->decimal_precision	= 2;
$config['currency']['IQD']->decimal_symbol		= '.';
$config['currency']['IQD']->thousands_seperator	= ',';

$config['currency']['ILS']						= new stdClass();
$config['currency']['ILS']->code				= 'ILS';
$config['currency']['ILS']->symbol				= '&curren;';
$config['currency']['ILS']->symbol_position		= 'BEFORE';
$config['currency']['ILS']->label				= 'Israeli New Sheqel';
$config['currency']['ILS']->decimal_precision	= 2;
$config['currency']['ILS']->decimal_symbol		= '.';
$config['currency']['ILS']->thousands_seperator	= ',';

$config['currency']['JMD']						= new stdClass();
$config['currency']['JMD']->code				= 'JMD';
$config['currency']['JMD']->symbol				= '&curren;';
$config['currency']['JMD']->symbol_position		= 'BEFORE';
$config['currency']['JMD']->label				= 'Jamaican Dollar';
$config['currency']['JMD']->decimal_precision	= 2;
$config['currency']['JMD']->decimal_symbol		= '.';
$config['currency']['JMD']->thousands_seperator	= ',';

$config['currency']['JPY']						= new stdClass();
$config['currency']['JPY']->code				= 'JPY';
$config['currency']['JPY']->symbol				= '&yen;';
$config['currency']['JPY']->symbol_position		= 'BEFORE';
$config['currency']['JPY']->label				= 'Japanese Yen';
$config['currency']['JPY']->decimal_precision	= 0;
$config['currency']['JPY']->decimal_symbol		= '.';
$config['currency']['JPY']->thousands_seperator	= ',';

$config['currency']['JEP']						= new stdClass();
$config['currency']['JEP']->code				= 'JEP';
$config['currency']['JEP']->symbol				= '&curren;';
$config['currency']['JEP']->symbol_position		= 'BEFORE';
$config['currency']['JEP']->label				= 'Jersey Pound';
$config['currency']['JEP']->decimal_precision	= 2;
$config['currency']['JEP']->decimal_symbol		= '.';
$config['currency']['JEP']->thousands_seperator	= ',';

$config['currency']['JOD']						= new stdClass();
$config['currency']['JOD']->code				= 'JOD';
$config['currency']['JOD']->symbol				= '&curren;';
$config['currency']['JOD']->symbol_position		= 'BEFORE';
$config['currency']['JOD']->label				= 'Jordanian Dinar';
$config['currency']['JOD']->decimal_precision	= 2;
$config['currency']['JOD']->decimal_symbol		= '.';
$config['currency']['JOD']->thousands_seperator	= ',';

$config['currency']['KZT']						= new stdClass();
$config['currency']['KZT']->code				= 'KZT';
$config['currency']['KZT']->symbol				= '&curren;';
$config['currency']['KZT']->symbol_position		= 'BEFORE';
$config['currency']['KZT']->label				= 'Kazakhstani Tenge';
$config['currency']['KZT']->decimal_precision	= 2;
$config['currency']['KZT']->decimal_symbol		= '.';
$config['currency']['KZT']->thousands_seperator	= ',';

$config['currency']['KES']						= new stdClass();
$config['currency']['KES']->code				= 'KES';
$config['currency']['KES']->symbol				= '&curren;';
$config['currency']['KES']->symbol_position		= 'BEFORE';
$config['currency']['KES']->label				= 'Kenyan Shilling';
$config['currency']['KES']->decimal_precision	= 2;
$config['currency']['KES']->decimal_symbol		= '.';
$config['currency']['KES']->thousands_seperator	= ',';

$config['currency']['KWD']						= new stdClass();
$config['currency']['KWD']->code				= 'KWD';
$config['currency']['KWD']->symbol				= '&curren;';
$config['currency']['KWD']->symbol_position		= 'BEFORE';
$config['currency']['KWD']->label				= 'Kuwaiti Dinar';
$config['currency']['KWD']->decimal_precision	= 2;
$config['currency']['KWD']->decimal_symbol		= '.';
$config['currency']['KWD']->thousands_seperator	= ',';

$config['currency']['KGS']						= new stdClass();
$config['currency']['KGS']->code				= 'KGS';
$config['currency']['KGS']->symbol				= '&curren;';
$config['currency']['KGS']->symbol_position		= 'BEFORE';
$config['currency']['KGS']->label				= 'Kyrgystani Som';
$config['currency']['KGS']->decimal_precision	= 2;
$config['currency']['KGS']->decimal_symbol		= '.';
$config['currency']['KGS']->thousands_seperator	= ',';

$config['currency']['LAK']						= new stdClass();
$config['currency']['LAK']->code				= 'LAK';
$config['currency']['LAK']->symbol				= '&curren;';
$config['currency']['LAK']->symbol_position		= 'BEFORE';
$config['currency']['LAK']->label				= 'Laotian Kip';
$config['currency']['LAK']->decimal_precision	= 2;
$config['currency']['LAK']->decimal_symbol		= '.';
$config['currency']['LAK']->thousands_seperator	= ',';

$config['currency']['LVL']						= new stdClass();
$config['currency']['LVL']->code				= 'LVL';
$config['currency']['LVL']->symbol				= '&curren;';
$config['currency']['LVL']->symbol_position		= 'BEFORE';
$config['currency']['LVL']->label				= 'Latvian Lats';
$config['currency']['LVL']->decimal_precision	= 2;
$config['currency']['LVL']->decimal_symbol		= '.';
$config['currency']['LVL']->thousands_seperator	= ',';

$config['currency']['LBP']						= new stdClass();
$config['currency']['LBP']->code				= 'LBP';
$config['currency']['LBP']->symbol				= '&curren;';
$config['currency']['LBP']->symbol_position		= 'BEFORE';
$config['currency']['LBP']->label				= 'Lebanese Pound';
$config['currency']['LBP']->decimal_precision	= 2;
$config['currency']['LBP']->decimal_symbol		= '.';
$config['currency']['LBP']->thousands_seperator	= ',';

$config['currency']['LSL']						= new stdClass();
$config['currency']['LSL']->code				= 'LSL';
$config['currency']['LSL']->symbol				= '&curren;';
$config['currency']['LSL']->symbol_position		= 'BEFORE';
$config['currency']['LSL']->label				= 'Lesotho Loti';
$config['currency']['LSL']->decimal_precision	= 2;
$config['currency']['LSL']->decimal_symbol		= '.';
$config['currency']['LSL']->thousands_seperator	= ',';

$config['currency']['LRD']						= new stdClass();
$config['currency']['LRD']->code				= 'LRD';
$config['currency']['LRD']->symbol				= '&curren;';
$config['currency']['LRD']->symbol_position		= 'BEFORE';
$config['currency']['LRD']->label				= 'Liberian Dollar';
$config['currency']['LRD']->decimal_precision	= 2;
$config['currency']['LRD']->decimal_symbol		= '.';
$config['currency']['LRD']->thousands_seperator	= ',';

$config['currency']['LYD']						= new stdClass();
$config['currency']['LYD']->code				= 'LYD';
$config['currency']['LYD']->symbol				= '&curren;';
$config['currency']['LYD']->symbol_position		= 'BEFORE';
$config['currency']['LYD']->label				= 'Libyan Dinar';
$config['currency']['LYD']->decimal_precision	= 2;
$config['currency']['LYD']->decimal_symbol		= '.';
$config['currency']['LYD']->thousands_seperator	= ',';

$config['currency']['LTL']						= new stdClass();
$config['currency']['LTL']->code				= 'LTL';
$config['currency']['LTL']->symbol				= '&curren;';
$config['currency']['LTL']->symbol_position		= 'BEFORE';
$config['currency']['LTL']->label				= 'Lithuanian Litas';
$config['currency']['LTL']->decimal_precision	= 2;
$config['currency']['LTL']->decimal_symbol		= '.';
$config['currency']['LTL']->thousands_seperator	= ',';

$config['currency']['MOP']						= new stdClass();
$config['currency']['MOP']->code				= 'MOP';
$config['currency']['MOP']->symbol				= '&curren;';
$config['currency']['MOP']->symbol_position		= 'BEFORE';
$config['currency']['MOP']->label				= 'Macanese Pataca';
$config['currency']['MOP']->decimal_precision	= 2;
$config['currency']['MOP']->decimal_symbol		= '.';
$config['currency']['MOP']->thousands_seperator	= ',';

$config['currency']['MKD']						= new stdClass();
$config['currency']['MKD']->code				= 'MKD';
$config['currency']['MKD']->symbol				= '&curren;';
$config['currency']['MKD']->symbol_position		= 'BEFORE';
$config['currency']['MKD']->label				= 'Macedonian Denar';
$config['currency']['MKD']->decimal_precision	= 2;
$config['currency']['MKD']->decimal_symbol		= '.';
$config['currency']['MKD']->thousands_seperator	= ',';

$config['currency']['MGA']						= new stdClass();
$config['currency']['MGA']->code				= 'MGA';
$config['currency']['MGA']->symbol				= '&curren;';
$config['currency']['MGA']->symbol_position		= 'BEFORE';
$config['currency']['MGA']->label				= 'Malagasy Ariary';
$config['currency']['MGA']->decimal_precision	= 2;
$config['currency']['MGA']->decimal_symbol		= '.';
$config['currency']['MGA']->thousands_seperator	= ',';

$config['currency']['MWK']						= new stdClass();
$config['currency']['MWK']->code				= 'MWK';
$config['currency']['MWK']->symbol				= '&curren;';
$config['currency']['MWK']->symbol_position		= 'BEFORE';
$config['currency']['MWK']->label				= 'Malawian Kwacha';
$config['currency']['MWK']->decimal_precision	= 2;
$config['currency']['MWK']->decimal_symbol		= '.';
$config['currency']['MWK']->thousands_seperator	= ',';

$config['currency']['MYR']						= new stdClass();
$config['currency']['MYR']->code				= 'MYR';
$config['currency']['MYR']->symbol				= '&curren;';
$config['currency']['MYR']->symbol_position		= 'BEFORE';
$config['currency']['MYR']->label				= 'Malaysian Ringgit';
$config['currency']['MYR']->decimal_precision	= 2;
$config['currency']['MYR']->decimal_symbol		= '.';
$config['currency']['MYR']->thousands_seperator	= ',';

$config['currency']['MVR']						= new stdClass();
$config['currency']['MVR']->code				= 'MVR';
$config['currency']['MVR']->symbol				= '&curren;';
$config['currency']['MVR']->symbol_position		= 'BEFORE';
$config['currency']['MVR']->label				= 'Maldivian Rufiyaa';
$config['currency']['MVR']->decimal_precision	= 2;
$config['currency']['MVR']->decimal_symbol		= '.';
$config['currency']['MVR']->thousands_seperator	= ',';

$config['currency']['MTL']						= new stdClass();
$config['currency']['MTL']->code				= 'MTL';
$config['currency']['MTL']->symbol				= '&curren;';
$config['currency']['MTL']->symbol_position		= 'BEFORE';
$config['currency']['MTL']->label				= 'Maltese Lira';
$config['currency']['MTL']->decimal_precision	= 2;
$config['currency']['MTL']->decimal_symbol		= '.';
$config['currency']['MTL']->thousands_seperator	= ',';

$config['currency']['MRO']						= new stdClass();
$config['currency']['MRO']->code				= 'MRO';
$config['currency']['MRO']->symbol				= '&curren;';
$config['currency']['MRO']->symbol_position		= 'BEFORE';
$config['currency']['MRO']->label				= 'Mauritanian Ouguiya';
$config['currency']['MRO']->decimal_precision	= 2;
$config['currency']['MRO']->decimal_symbol		= '.';
$config['currency']['MRO']->thousands_seperator	= ',';

$config['currency']['MUR']						= new stdClass();
$config['currency']['MUR']->code				= 'MUR';
$config['currency']['MUR']->symbol				= '&curren;';
$config['currency']['MUR']->symbol_position		= 'BEFORE';
$config['currency']['MUR']->label				= 'Mauritian Rupee';
$config['currency']['MUR']->decimal_precision	= 2;
$config['currency']['MUR']->decimal_symbol		= '.';
$config['currency']['MUR']->thousands_seperator	= ',';

$config['currency']['MXN']						= new stdClass();
$config['currency']['MXN']->code				= 'MXN';
$config['currency']['MXN']->symbol				= '&curren;';
$config['currency']['MXN']->symbol_position		= 'BEFORE';
$config['currency']['MXN']->label				= 'Mexican Peso';
$config['currency']['MXN']->decimal_precision	= 2;
$config['currency']['MXN']->decimal_symbol		= '.';
$config['currency']['MXN']->thousands_seperator	= ',';

$config['currency']['MDL']						= new stdClass();
$config['currency']['MDL']->code				= 'MDL';
$config['currency']['MDL']->symbol				= '&curren;';
$config['currency']['MDL']->symbol_position		= 'BEFORE';
$config['currency']['MDL']->label				= 'Moldovan Leu';
$config['currency']['MDL']->decimal_precision	= 2;
$config['currency']['MDL']->decimal_symbol		= '.';
$config['currency']['MDL']->thousands_seperator	= ',';

$config['currency']['MNT']						= new stdClass();
$config['currency']['MNT']->code				= 'MNT';
$config['currency']['MNT']->symbol				= '&curren;';
$config['currency']['MNT']->symbol_position		= 'BEFORE';
$config['currency']['MNT']->label				= 'Mongolian Tugrik';
$config['currency']['MNT']->decimal_precision	= 2;
$config['currency']['MNT']->decimal_symbol		= '.';
$config['currency']['MNT']->thousands_seperator	= ',';

$config['currency']['MAD']						= new stdClass();
$config['currency']['MAD']->code				= 'MAD';
$config['currency']['MAD']->symbol				= '&curren;';
$config['currency']['MAD']->symbol_position		= 'BEFORE';
$config['currency']['MAD']->label				= 'Moroccan Dirham';
$config['currency']['MAD']->decimal_precision	= 2;
$config['currency']['MAD']->decimal_symbol		= '.';
$config['currency']['MAD']->thousands_seperator	= ',';

$config['currency']['MZN']						= new stdClass();
$config['currency']['MZN']->code				= 'MZN';
$config['currency']['MZN']->symbol				= '&curren;';
$config['currency']['MZN']->symbol_position		= 'BEFORE';
$config['currency']['MZN']->label				= 'Mozambican Metical';
$config['currency']['MZN']->decimal_precision	= 2;
$config['currency']['MZN']->decimal_symbol		= '.';
$config['currency']['MZN']->thousands_seperator	= ',';

$config['currency']['NAD']						= new stdClass();
$config['currency']['NAD']->code				= 'NAD';
$config['currency']['NAD']->symbol				= '&curren;';
$config['currency']['NAD']->symbol_position		= 'BEFORE';
$config['currency']['NAD']->label				= 'Namibian Dollar';
$config['currency']['NAD']->decimal_precision	= 2;
$config['currency']['NAD']->decimal_symbol		= '.';
$config['currency']['NAD']->thousands_seperator	= ',';

$config['currency']['NPR']						= new stdClass();
$config['currency']['NPR']->code				= 'NPR';
$config['currency']['NPR']->symbol				= '&curren;';
$config['currency']['NPR']->symbol_position		= 'BEFORE';
$config['currency']['NPR']->label				= 'Nepalese Rupee';
$config['currency']['NPR']->decimal_precision	= 2;
$config['currency']['NPR']->decimal_symbol		= '.';
$config['currency']['NPR']->thousands_seperator	= ',';

$config['currency']['ANG']						= new stdClass();
$config['currency']['ANG']->code				= 'ANG';
$config['currency']['ANG']->symbol				= '&curren;';
$config['currency']['ANG']->symbol_position		= 'BEFORE';
$config['currency']['ANG']->label				= 'Netherlands Antillean Guilder';
$config['currency']['ANG']->decimal_precision	= 2;
$config['currency']['ANG']->decimal_symbol		= '.';
$config['currency']['ANG']->thousands_seperator	= ',';

$config['currency']['TWD']						= new stdClass();
$config['currency']['TWD']->code				= 'TWD';
$config['currency']['TWD']->symbol				= '&curren;';
$config['currency']['TWD']->symbol_position		= 'BEFORE';
$config['currency']['TWD']->label				= 'New Taiwan Dollar';
$config['currency']['TWD']->decimal_precision	= 2;
$config['currency']['TWD']->decimal_symbol		= '.';
$config['currency']['TWD']->thousands_seperator	= ',';

$config['currency']['NZD']						= new stdClass();
$config['currency']['NZD']->code				= 'NZD';
$config['currency']['NZD']->symbol				= '&curren;';
$config['currency']['NZD']->symbol_position		= 'BEFORE';
$config['currency']['NZD']->label				= 'New Zealand Dollar';
$config['currency']['NZD']->decimal_precision	= 2;
$config['currency']['NZD']->decimal_symbol		= '.';
$config['currency']['NZD']->thousands_seperator	= ',';

$config['currency']['NIO']						= new stdClass();
$config['currency']['NIO']->code				= 'NIO';
$config['currency']['NIO']->symbol				= '&curren;';
$config['currency']['NIO']->symbol_position		= 'BEFORE';
$config['currency']['NIO']->label				= 'Nicaraguan Córdoba';
$config['currency']['NIO']->decimal_precision	= 2;
$config['currency']['NIO']->decimal_symbol		= '.';
$config['currency']['NIO']->thousands_seperator	= ',';

$config['currency']['NGN']						= new stdClass();
$config['currency']['NGN']->code				= 'NGN';
$config['currency']['NGN']->symbol				= '&curren;';
$config['currency']['NGN']->symbol_position		= 'BEFORE';
$config['currency']['NGN']->label				= 'Nigerian Naira';
$config['currency']['NGN']->decimal_precision	= 2;
$config['currency']['NGN']->decimal_symbol		= '.';
$config['currency']['NGN']->thousands_seperator	= ',';

$config['currency']['KPW']						= new stdClass();
$config['currency']['KPW']->code				= 'KPW';
$config['currency']['KPW']->symbol				= '&curren;';
$config['currency']['KPW']->symbol_position		= 'BEFORE';
$config['currency']['KPW']->label				= 'North Korean Won';
$config['currency']['KPW']->decimal_precision	= 2;
$config['currency']['KPW']->decimal_symbol		= '.';
$config['currency']['KPW']->thousands_seperator	= ',';

$config['currency']['NOK']						= new stdClass();
$config['currency']['NOK']->code				= 'NOK';
$config['currency']['NOK']->symbol				= '&curren;';
$config['currency']['NOK']->symbol_position		= 'BEFORE';
$config['currency']['NOK']->label				= 'Norwegian Krone';
$config['currency']['NOK']->decimal_precision	= 2;
$config['currency']['NOK']->decimal_symbol		= '.';
$config['currency']['NOK']->thousands_seperator	= ',';

$config['currency']['OMR']						= new stdClass();
$config['currency']['OMR']->code				= 'OMR';
$config['currency']['OMR']->symbol				= '&curren;';
$config['currency']['OMR']->symbol_position		= 'BEFORE';
$config['currency']['OMR']->label				= 'Omani Rial';
$config['currency']['OMR']->decimal_precision	= 2;
$config['currency']['OMR']->decimal_symbol		= '.';
$config['currency']['OMR']->thousands_seperator	= ',';

$config['currency']['PKR']						= new stdClass();
$config['currency']['PKR']->code				= 'PKR';
$config['currency']['PKR']->symbol				= '&curren;';
$config['currency']['PKR']->symbol_position		= 'BEFORE';
$config['currency']['PKR']->label				= 'Pakistani Rupee';
$config['currency']['PKR']->decimal_precision	= 2;
$config['currency']['PKR']->decimal_symbol		= '.';
$config['currency']['PKR']->thousands_seperator	= ',';

$config['currency']['PAB']						= new stdClass();
$config['currency']['PAB']->code				= 'PAB';
$config['currency']['PAB']->symbol				= '&curren;';
$config['currency']['PAB']->symbol_position		= 'BEFORE';
$config['currency']['PAB']->label				= 'Panamanian Balboa';
$config['currency']['PAB']->decimal_precision	= 2;
$config['currency']['PAB']->decimal_symbol		= '.';
$config['currency']['PAB']->thousands_seperator	= ',';

$config['currency']['PGK']						= new stdClass();
$config['currency']['PGK']->code				= 'PGK';
$config['currency']['PGK']->symbol				= '&curren;';
$config['currency']['PGK']->symbol_position		= 'BEFORE';
$config['currency']['PGK']->label				= 'Papua New Guinean Kina';
$config['currency']['PGK']->decimal_precision	= 2;
$config['currency']['PGK']->decimal_symbol		= '.';
$config['currency']['PGK']->thousands_seperator	= ',';

$config['currency']['PYG']						= new stdClass();
$config['currency']['PYG']->code				= 'PYG';
$config['currency']['PYG']->symbol				= '&curren;';
$config['currency']['PYG']->symbol_position		= 'BEFORE';
$config['currency']['PYG']->label				= 'Paraguayan Guarani';
$config['currency']['PYG']->decimal_precision	= 2;
$config['currency']['PYG']->decimal_symbol		= '.';
$config['currency']['PYG']->thousands_seperator	= ',';

$config['currency']['PEN']						= new stdClass();
$config['currency']['PEN']->code				= 'PEN';
$config['currency']['PEN']->symbol				= '&curren;';
$config['currency']['PEN']->symbol_position		= 'BEFORE';
$config['currency']['PEN']->label				= 'Peruvian Nuevo Sol';
$config['currency']['PEN']->decimal_precision	= 2;
$config['currency']['PEN']->decimal_symbol		= '.';
$config['currency']['PEN']->thousands_seperator	= ',';

$config['currency']['PHP']						= new stdClass();
$config['currency']['PHP']->code				= 'PHP';
$config['currency']['PHP']->symbol				= '&curren;';
$config['currency']['PHP']->symbol_position		= 'BEFORE';
$config['currency']['PHP']->label				= 'Philippine Peso';
$config['currency']['PHP']->decimal_precision	= 2;
$config['currency']['PHP']->decimal_symbol		= '.';
$config['currency']['PHP']->thousands_seperator	= ',';

$config['currency']['PLN']						= new stdClass();
$config['currency']['PLN']->code				= 'PLN';
$config['currency']['PLN']->symbol				= '&curren;';
$config['currency']['PLN']->symbol_position		= 'BEFORE';
$config['currency']['PLN']->label				= 'Polish Zloty';
$config['currency']['PLN']->decimal_precision	= 2;
$config['currency']['PLN']->decimal_symbol		= '.';
$config['currency']['PLN']->thousands_seperator	= ',';

$config['currency']['QAR']						= new stdClass();
$config['currency']['QAR']->code				= 'QAR';
$config['currency']['QAR']->symbol				= '&curren;';
$config['currency']['QAR']->symbol_position		= 'BEFORE';
$config['currency']['QAR']->label				= 'Qatari Rial';
$config['currency']['QAR']->decimal_precision	= 2;
$config['currency']['QAR']->decimal_symbol		= '.';
$config['currency']['QAR']->thousands_seperator	= ',';

$config['currency']['RON']						= new stdClass();
$config['currency']['RON']->code				= 'RON';
$config['currency']['RON']->symbol				= '&curren;';
$config['currency']['RON']->symbol_position		= 'BEFORE';
$config['currency']['RON']->label				= 'Romanian Leu';
$config['currency']['RON']->decimal_precision	= 2;
$config['currency']['RON']->decimal_symbol		= '.';
$config['currency']['RON']->thousands_seperator	= ',';

$config['currency']['RUB']						= new stdClass();
$config['currency']['RUB']->code				= 'RUB';
$config['currency']['RUB']->symbol				= '&curren;';
$config['currency']['RUB']->symbol_position		= 'BEFORE';
$config['currency']['RUB']->label				= 'Russian Ruble';
$config['currency']['RUB']->decimal_precision	= 2;
$config['currency']['RUB']->decimal_symbol		= '.';
$config['currency']['RUB']->thousands_seperator	= ',';

$config['currency']['RWF']						= new stdClass();
$config['currency']['RWF']->code				= 'RWF';
$config['currency']['RWF']->symbol				= '&curren;';
$config['currency']['RWF']->symbol_position		= 'BEFORE';
$config['currency']['RWF']->label				= 'Rwandan Franc';
$config['currency']['RWF']->decimal_precision	= 2;
$config['currency']['RWF']->decimal_symbol		= '.';
$config['currency']['RWF']->thousands_seperator	= ',';

$config['currency']['SHP']						= new stdClass();
$config['currency']['SHP']->code				= 'SHP';
$config['currency']['SHP']->symbol				= '&curren;';
$config['currency']['SHP']->symbol_position		= 'BEFORE';
$config['currency']['SHP']->label				= 'Saint Helena Pound';
$config['currency']['SHP']->decimal_precision	= 2;
$config['currency']['SHP']->decimal_symbol		= '.';
$config['currency']['SHP']->thousands_seperator	= ',';

$config['currency']['SVC']						= new stdClass();
$config['currency']['SVC']->code				= 'SVC';
$config['currency']['SVC']->symbol				= '&curren;';
$config['currency']['SVC']->symbol_position		= 'BEFORE';
$config['currency']['SVC']->label				= 'Salvadoran Colón';
$config['currency']['SVC']->decimal_precision	= 2;
$config['currency']['SVC']->decimal_symbol		= '.';
$config['currency']['SVC']->thousands_seperator	= ',';

$config['currency']['WST']						= new stdClass();
$config['currency']['WST']->code				= 'WST';
$config['currency']['WST']->symbol				= '&curren;';
$config['currency']['WST']->symbol_position		= 'BEFORE';
$config['currency']['WST']->label				= 'Samoan Tala';
$config['currency']['WST']->decimal_precision	= 2;
$config['currency']['WST']->decimal_symbol		= '.';
$config['currency']['WST']->thousands_seperator	= ',';

$config['currency']['SAR']						= new stdClass();
$config['currency']['SAR']->code				= 'SAR';
$config['currency']['SAR']->symbol				= '&curren;';
$config['currency']['SAR']->symbol_position		= 'BEFORE';
$config['currency']['SAR']->label				= 'Saudi Riyal';
$config['currency']['SAR']->decimal_precision	= 2;
$config['currency']['SAR']->decimal_symbol		= '.';
$config['currency']['SAR']->thousands_seperator	= ',';

$config['currency']['RSD']						= new stdClass();
$config['currency']['RSD']->code				= 'RSD';
$config['currency']['RSD']->symbol				= '&curren;';
$config['currency']['RSD']->symbol_position		= 'BEFORE';
$config['currency']['RSD']->label				= 'Serbian Dinar';
$config['currency']['RSD']->decimal_precision	= 2;
$config['currency']['RSD']->decimal_symbol		= '.';
$config['currency']['RSD']->thousands_seperator	= ',';

$config['currency']['SCR']						= new stdClass();
$config['currency']['SCR']->code				= 'SCR';
$config['currency']['SCR']->symbol				= '&curren;';
$config['currency']['SCR']->symbol_position		= 'BEFORE';
$config['currency']['SCR']->label				= 'Seychellois Rupee';
$config['currency']['SCR']->decimal_precision	= 2;
$config['currency']['SCR']->decimal_symbol		= '.';
$config['currency']['SCR']->thousands_seperator	= ',';

$config['currency']['SLL']						= new stdClass();
$config['currency']['SLL']->code				= 'SLL';
$config['currency']['SLL']->symbol				= '&curren;';
$config['currency']['SLL']->symbol_position		= 'BEFORE';
$config['currency']['SLL']->label				= 'Sierra Leonean Leone';
$config['currency']['SLL']->decimal_precision	= 2;
$config['currency']['SLL']->decimal_symbol		= '.';
$config['currency']['SLL']->thousands_seperator	= ',';

$config['currency']['XAG']						= new stdClass();
$config['currency']['XAG']->code				= 'XAG';
$config['currency']['XAG']->symbol				= '&curren;';
$config['currency']['XAG']->symbol_position		= 'BEFORE';
$config['currency']['XAG']->label				= 'Silver (troy ounce)';
$config['currency']['XAG']->decimal_precision	= 2;
$config['currency']['XAG']->decimal_symbol		= '.';
$config['currency']['XAG']->thousands_seperator	= ',';

$config['currency']['SGD']						= new stdClass();
$config['currency']['SGD']->code				= 'SGD';
$config['currency']['SGD']->symbol				= '&curren;';
$config['currency']['SGD']->symbol_position		= 'BEFORE';
$config['currency']['SGD']->label				= 'Singapore Dollar';
$config['currency']['SGD']->decimal_precision	= 2;
$config['currency']['SGD']->decimal_symbol		= '.';
$config['currency']['SGD']->thousands_seperator	= ',';

$config['currency']['SBD']						= new stdClass();
$config['currency']['SBD']->code				= 'SBD';
$config['currency']['SBD']->symbol				= '&curren;';
$config['currency']['SBD']->symbol_position		= 'BEFORE';
$config['currency']['SBD']->label				= 'Solomon Islands Dollar';
$config['currency']['SBD']->decimal_precision	= 2;
$config['currency']['SBD']->decimal_symbol		= '.';
$config['currency']['SBD']->thousands_seperator	= ',';

$config['currency']['SOS']						= new stdClass();
$config['currency']['SOS']->code				= 'SOS';
$config['currency']['SOS']->symbol				= '&curren;';
$config['currency']['SOS']->symbol_position		= 'BEFORE';
$config['currency']['SOS']->label				= 'Somali Shilling';
$config['currency']['SOS']->decimal_precision	= 2;
$config['currency']['SOS']->decimal_symbol		= '.';
$config['currency']['SOS']->thousands_seperator	= ',';

$config['currency']['ZAR']						= new stdClass();
$config['currency']['ZAR']->code				= 'ZAR';
$config['currency']['ZAR']->symbol				= '&curren;';
$config['currency']['ZAR']->symbol_position		= 'BEFORE';
$config['currency']['ZAR']->label				= 'South African Rand';
$config['currency']['ZAR']->decimal_precision	= 2;
$config['currency']['ZAR']->decimal_symbol		= '.';
$config['currency']['ZAR']->thousands_seperator	= ',';

$config['currency']['KRW']						= new stdClass();
$config['currency']['KRW']->code				= 'KRW';
$config['currency']['KRW']->symbol				= '&curren;';
$config['currency']['KRW']->symbol_position		= 'BEFORE';
$config['currency']['KRW']->label				= 'South Korean Won';
$config['currency']['KRW']->decimal_precision	= 2;
$config['currency']['KRW']->decimal_symbol		= '.';
$config['currency']['KRW']->thousands_seperator	= ',';

$config['currency']['XDR']						= new stdClass();
$config['currency']['XDR']->code				= 'XDR';
$config['currency']['XDR']->symbol				= '&curren;';
$config['currency']['XDR']->symbol_position		= 'BEFORE';
$config['currency']['XDR']->label				= 'Special Drawing Rights';
$config['currency']['XDR']->decimal_precision	= 2;
$config['currency']['XDR']->decimal_symbol		= '.';
$config['currency']['XDR']->thousands_seperator	= ',';

$config['currency']['LKR']						= new stdClass();
$config['currency']['LKR']->code				= 'LKR';
$config['currency']['LKR']->symbol				= '&curren;';
$config['currency']['LKR']->symbol_position		= 'BEFORE';
$config['currency']['LKR']->label				= 'Sri Lankan Rupee';
$config['currency']['LKR']->decimal_precision	= 2;
$config['currency']['LKR']->decimal_symbol		= '.';
$config['currency']['LKR']->thousands_seperator	= ',';

$config['currency']['SDG']						= new stdClass();
$config['currency']['SDG']->code				= 'SDG';
$config['currency']['SDG']->symbol				= '&curren;';
$config['currency']['SDG']->symbol_position		= 'BEFORE';
$config['currency']['SDG']->label				= 'Sudanese Pound';
$config['currency']['SDG']->decimal_precision	= 2;
$config['currency']['SDG']->decimal_symbol		= '.';
$config['currency']['SDG']->thousands_seperator	= ',';

$config['currency']['SRD']						= new stdClass();
$config['currency']['SRD']->code				= 'SRD';
$config['currency']['SRD']->symbol				= '&curren;';
$config['currency']['SRD']->symbol_position		= 'BEFORE';
$config['currency']['SRD']->label				= 'Surinamese Dollar';
$config['currency']['SRD']->decimal_precision	= 2;
$config['currency']['SRD']->decimal_symbol		= '.';
$config['currency']['SRD']->thousands_seperator	= ',';

$config['currency']['SZL']						= new stdClass();
$config['currency']['SZL']->code				= 'SZL';
$config['currency']['SZL']->symbol				= '&curren;';
$config['currency']['SZL']->symbol_position		= 'BEFORE';
$config['currency']['SZL']->label				= 'Swazi Lilangeni';
$config['currency']['SZL']->decimal_precision	= 2;
$config['currency']['SZL']->decimal_symbol		= '.';
$config['currency']['SZL']->thousands_seperator	= ',';

$config['currency']['SEK']						= new stdClass();
$config['currency']['SEK']->code				= 'SEK';
$config['currency']['SEK']->symbol				= '&curren;';
$config['currency']['SEK']->symbol_position		= 'BEFORE';
$config['currency']['SEK']->label				= 'Swedish Krona';
$config['currency']['SEK']->decimal_precision	= 2;
$config['currency']['SEK']->decimal_symbol		= '.';
$config['currency']['SEK']->thousands_seperator	= ',';

$config['currency']['CHF']						= new stdClass();
$config['currency']['CHF']->code				= 'CHF';
$config['currency']['CHF']->symbol				= '&curren;';
$config['currency']['CHF']->symbol_position		= 'BEFORE';
$config['currency']['CHF']->label				= 'Swiss Franc';
$config['currency']['CHF']->decimal_precision	= 2;
$config['currency']['CHF']->decimal_symbol		= '.';
$config['currency']['CHF']->thousands_seperator	= ',';

$config['currency']['SYP']						= new stdClass();
$config['currency']['SYP']->code				= 'SYP';
$config['currency']['SYP']->symbol				= '&curren;';
$config['currency']['SYP']->symbol_position		= 'BEFORE';
$config['currency']['SYP']->label				= 'Syrian Pound';
$config['currency']['SYP']->decimal_precision	= 2;
$config['currency']['SYP']->decimal_symbol		= '.';
$config['currency']['SYP']->thousands_seperator	= ',';

$config['currency']['STD']						= new stdClass();
$config['currency']['STD']->code				= 'STD';
$config['currency']['STD']->symbol				= '&curren;';
$config['currency']['STD']->symbol_position		= 'BEFORE';
$config['currency']['STD']->label				= 'São Tomé and Príncipe Dobra';
$config['currency']['STD']->decimal_precision	= 2;
$config['currency']['STD']->decimal_symbol		= '.';
$config['currency']['STD']->thousands_seperator	= ',';

$config['currency']['TJS']						= new stdClass();
$config['currency']['TJS']->code				= 'TJS';
$config['currency']['TJS']->symbol				= '&curren;';
$config['currency']['TJS']->symbol_position		= 'BEFORE';
$config['currency']['TJS']->label				= 'Tajikistani Somoni';
$config['currency']['TJS']->decimal_precision	= 2;
$config['currency']['TJS']->decimal_symbol		= '.';
$config['currency']['TJS']->thousands_seperator	= ',';

$config['currency']['TZS']						= new stdClass();
$config['currency']['TZS']->code				= 'TZS';
$config['currency']['TZS']->symbol				= '&curren;';
$config['currency']['TZS']->symbol_position		= 'BEFORE';
$config['currency']['TZS']->label				= 'Tanzanian Shilling';
$config['currency']['TZS']->decimal_precision	= 2;
$config['currency']['TZS']->decimal_symbol		= '.';
$config['currency']['TZS']->thousands_seperator	= ',';

$config['currency']['THB']						= new stdClass();
$config['currency']['THB']->code				= 'THB';
$config['currency']['THB']->symbol				= '&curren;';
$config['currency']['THB']->symbol_position		= 'BEFORE';
$config['currency']['THB']->label				= 'Thai Baht';
$config['currency']['THB']->decimal_precision	= 2;
$config['currency']['THB']->decimal_symbol		= '.';
$config['currency']['THB']->thousands_seperator	= ',';

$config['currency']['TOP']						= new stdClass();
$config['currency']['TOP']->code				= 'TOP';
$config['currency']['TOP']->symbol				= '&curren;';
$config['currency']['TOP']->symbol_position		= 'BEFORE';
$config['currency']['TOP']->label				= 'Tongan Paʻanga';
$config['currency']['TOP']->decimal_precision	= 2;
$config['currency']['TOP']->decimal_symbol		= '.';
$config['currency']['TOP']->thousands_seperator	= ',';

$config['currency']['TTD']						= new stdClass();
$config['currency']['TTD']->code				= 'TTD';
$config['currency']['TTD']->symbol				= '&curren;';
$config['currency']['TTD']->symbol_position		= 'BEFORE';
$config['currency']['TTD']->label				= 'Trinidad and Tobago Dollar';
$config['currency']['TTD']->decimal_precision	= 2;
$config['currency']['TTD']->decimal_symbol		= '.';
$config['currency']['TTD']->thousands_seperator	= ',';

$config['currency']['TND']						= new stdClass();
$config['currency']['TND']->code				= 'TND';
$config['currency']['TND']->symbol				= '&curren;';
$config['currency']['TND']->symbol_position		= 'BEFORE';
$config['currency']['TND']->label				= 'Tunisian Dinar';
$config['currency']['TND']->decimal_precision	= 2;
$config['currency']['TND']->decimal_symbol		= '.';
$config['currency']['TND']->thousands_seperator	= ',';

$config['currency']['TRY']						= new stdClass();
$config['currency']['TRY']->code				= 'TRY';
$config['currency']['TRY']->symbol				= '&curren;';
$config['currency']['TRY']->symbol_position		= 'BEFORE';
$config['currency']['TRY']->label				= 'Turkish Lira';
$config['currency']['TRY']->decimal_precision	= 2;
$config['currency']['TRY']->decimal_symbol		= '.';
$config['currency']['TRY']->thousands_seperator	= ',';

$config['currency']['TMT']						= new stdClass();
$config['currency']['TMT']->code				= 'TMT';
$config['currency']['TMT']->symbol				= '&curren;';
$config['currency']['TMT']->symbol_position		= 'BEFORE';
$config['currency']['TMT']->label				= 'Turkmenistani Manat';
$config['currency']['TMT']->decimal_precision	= 2;
$config['currency']['TMT']->decimal_symbol		= '.';
$config['currency']['TMT']->thousands_seperator	= ',';

$config['currency']['UGX']						= new stdClass();
$config['currency']['UGX']->code				= 'UGX';
$config['currency']['UGX']->symbol				= '&curren;';
$config['currency']['UGX']->symbol_position		= 'BEFORE';
$config['currency']['UGX']->label				= 'Ugandan Shilling';
$config['currency']['UGX']->decimal_precision	= 2;
$config['currency']['UGX']->decimal_symbol		= '.';
$config['currency']['UGX']->thousands_seperator	= ',';

$config['currency']['UAH']						= new stdClass();
$config['currency']['UAH']->code				= 'UAH';
$config['currency']['UAH']->symbol				= '&curren;';
$config['currency']['UAH']->symbol_position		= 'BEFORE';
$config['currency']['UAH']->label				= 'Ukrainian Hryvnia';
$config['currency']['UAH']->decimal_precision	= 2;
$config['currency']['UAH']->decimal_symbol		= '.';
$config['currency']['UAH']->thousands_seperator	= ',';

$config['currency']['AED']						= new stdClass();
$config['currency']['AED']->code				= 'AED';
$config['currency']['AED']->symbol				= '&curren;';
$config['currency']['AED']->symbol_position		= 'BEFORE';
$config['currency']['AED']->label				= 'United Arab Emirates Dirham';
$config['currency']['AED']->decimal_precision	= 2;
$config['currency']['AED']->decimal_symbol		= '.';
$config['currency']['AED']->thousands_seperator	= ',';

$config['currency']['UYU']						= new stdClass();
$config['currency']['UYU']->code				= 'UYU';
$config['currency']['UYU']->symbol				= '&curren;';
$config['currency']['UYU']->symbol_position		= 'BEFORE';
$config['currency']['UYU']->label				= 'Uruguayan Peso';
$config['currency']['UYU']->decimal_precision	= 2;
$config['currency']['UYU']->decimal_symbol		= '.';
$config['currency']['UYU']->thousands_seperator	= ',';

$config['currency']['UZS']						= new stdClass();
$config['currency']['UZS']->code				= 'UZS';
$config['currency']['UZS']->symbol				= '&curren;';
$config['currency']['UZS']->symbol_position		= 'BEFORE';
$config['currency']['UZS']->label				= 'Uzbekistan Som';
$config['currency']['UZS']->decimal_precision	= 2;
$config['currency']['UZS']->decimal_symbol		= '.';
$config['currency']['UZS']->thousands_seperator	= ',';

$config['currency']['VUV']						= new stdClass();
$config['currency']['VUV']->code				= 'VUV';
$config['currency']['VUV']->symbol				= '&curren;';
$config['currency']['VUV']->symbol_position		= 'BEFORE';
$config['currency']['VUV']->label				= 'Vanuatu Vatu';
$config['currency']['VUV']->decimal_precision	= 2;
$config['currency']['VUV']->decimal_symbol		= '.';
$config['currency']['VUV']->thousands_seperator	= ',';

$config['currency']['VEF']						= new stdClass();
$config['currency']['VEF']->code				= 'VEF';
$config['currency']['VEF']->symbol				= '&curren;';
$config['currency']['VEF']->symbol_position		= 'BEFORE';
$config['currency']['VEF']->label				= 'Venezuelan Bolívar Fuerte';
$config['currency']['VEF']->decimal_precision	= 2;
$config['currency']['VEF']->decimal_symbol		= '.';
$config['currency']['VEF']->thousands_seperator	= ',';

$config['currency']['VND']						= new stdClass();
$config['currency']['VND']->code				= 'VND';
$config['currency']['VND']->symbol				= '&curren;';
$config['currency']['VND']->symbol_position		= 'BEFORE';
$config['currency']['VND']->label				= 'Vietnamese Dong';
$config['currency']['VND']->decimal_precision	= 2;
$config['currency']['VND']->decimal_symbol		= '.';
$config['currency']['VND']->thousands_seperator	= ',';

$config['currency']['YER']						= new stdClass();
$config['currency']['YER']->code				= 'YER';
$config['currency']['YER']->symbol				= '&curren;';
$config['currency']['YER']->symbol_position		= 'BEFORE';
$config['currency']['YER']->label				= 'Yemeni Rial';
$config['currency']['YER']->decimal_precision	= 2;
$config['currency']['YER']->decimal_symbol		= '.';
$config['currency']['YER']->thousands_seperator	= ',';

$config['currency']['ZMW']						= new stdClass();
$config['currency']['ZMW']->code				= 'ZMW';
$config['currency']['ZMW']->symbol				= '&curren;';
$config['currency']['ZMW']->symbol_position		= 'BEFORE';
$config['currency']['ZMW']->label				= 'Zambian Kwacha';
$config['currency']['ZMW']->decimal_precision	= 2;
$config['currency']['ZMW']->decimal_symbol		= '.';
$config['currency']['ZMW']->thousands_seperator	= ',';

$config['currency']['ZMK']						= new stdClass();
$config['currency']['ZMK']->code				= 'ZMK';
$config['currency']['ZMK']->symbol				= '&curren;';
$config['currency']['ZMK']->symbol_position		= 'BEFORE';
$config['currency']['ZMK']->label				= 'Zambian Kwacha (pre-2013)';
$config['currency']['ZMK']->decimal_precision	= 2;
$config['currency']['ZMK']->decimal_symbol		= '.';
$config['currency']['ZMK']->thousands_seperator	= ',';

$config['currency']['ZWL']						= new stdClass();
$config['currency']['ZWL']->code				= 'ZWL';
$config['currency']['ZWL']->symbol				= '&curren;';
$config['currency']['ZWL']->symbol_position		= 'BEFORE';
$config['currency']['ZWL']->label				= 'Zimbabwean Dollar';
$config['currency']['ZWL']->decimal_precision	= 2;
$config['currency']['ZWL']->decimal_symbol		= '.';
$config['currency']['ZWL']->thousands_seperator	= ',';

/* End of file currency.php */
/* Location: ./config/currency.php */