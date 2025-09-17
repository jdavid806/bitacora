<?php

/**
 * convert a number to currency forma
 * 
 * @param number $number
 * @param string $currency
 * @return number with currency symbol
 */
if (!function_exists('to_currency')) {

    function to_currency($number = 0, $currency = "", $no_of_decimals = 2)
    {
        $decimal_separator = get_setting("decimal_separator");
        $thousand_separator = get_setting("thousand_separator");
        $number = is_null($number) ? 0 : $number;

        if (get_setting("no_of_decimals") == "0") {
            $no_of_decimals = 0;
        }

        $negative_sign = "";
        if ($number < 0) {
            $number = $number * -1;
            $negative_sign = "-";
        }
        if (!$currency) {
            $currency = get_setting("currency_symbol");
        }

        $currency_position = get_setting("currency_position");
        if (!$currency_position) {
            $currency_position = "left";
        }

        if ($decimal_separator === ",") {
            if ($thousand_separator !== " ") {
                $thousand_separator = ".";
            }

            if ($currency_position === "right") {
                return $negative_sign . number_format($number, $no_of_decimals, ",", $thousand_separator) . $currency;
            } else {
                return $negative_sign . $currency . number_format($number, $no_of_decimals, ",", $thousand_separator);
            }
        } else {
            if ($thousand_separator !== " ") {
                $thousand_separator = ",";
            }

            if ($currency_position === "right") {
                return $negative_sign . number_format($number, $no_of_decimals, ".", $thousand_separator) . $currency;
            } else {
                return $negative_sign . $currency . number_format($number, $no_of_decimals, ".", $thousand_separator);
            }
        }
    }
}

/**
 * convert a number to quantity format
 * 
 * @param number $number
 * @return number
 */
if (!function_exists('to_decimal_format')) {

    function to_decimal_format($number = 0, $follow_decimal_separator_setting = true)
    {
        $decimal_separator = get_setting("decimal_separator");
        $number = is_null($number) ? 0 : $number;

        $decimal = 0;
        if (is_numeric($number) && floor($number) != $number) {
            $decimal = get_setting("no_of_decimals") == "0" ? 0 : 2;
        }
        if ($follow_decimal_separator_setting && $decimal_separator === ",") {
            return number_format($number, $decimal, ",", ".");
        } else {
            return number_format($number, $decimal, ".", ",");
        }
    }
}

/**
 * convert a currency value to data format
 *  
 * @param number $currency
 * @return number
 */
if (!function_exists('unformat_currency')) {

    function unformat_currency($currency = "")
    {
        // remove everything except a digit "0-9", a comma ",", and a dot "."
        $new_money = preg_replace('/[^\d,-\.]/', '', $currency);
        $decimal_separator = get_setting("decimal_separator");
        if ($decimal_separator === ",") {
            $new_money = str_replace(".", "", $new_money);
            $new_money = str_replace(",", ".", $new_money);
        } else {
            $new_money = str_replace(",", "", $new_money);
        }
        return $new_money;
    }
}

/**
 * get array of international currency codes
 * 
 * @return array
 */
if (!function_exists('get_international_currency_code_list')) {

    function get_international_currency_code_list()
    {
        return array(
            "AED",
            "AFN",
            "ALL",
            "AMD",
            "ANG",
            "AOA",
            "ARS",
            "AUD",
            "AWG",
            "AZN",
            "BAM",
            "BBD",
            "BDT",
            "BGN",
            "BHD",
            "BIF",
            "BMD",
            "BND",
            "BOB",
            "BOV",
            "BRL",
            "BSD",
            "BTN",
            "BWP",
            "BYR",
            "BZD",
            "CAD",
            "CDF",
            "CHE",
            "CHF",
            "CHW",
            "CLF",
            "CLP",
            "CNY",
            "COP",
            "COU",
            "CRC",
            "CUC",
            "CUP",
            "CVE",
            "CZK",
            "DJF",
            "DKK",
            "DOP",
            "DZD",
            "EGP",
            "ERN",
            "ETB",
            "EUR",
            "FJD",
            "FKP",
            "GBP",
            "GEL",
            "GHS",
            "GIP",
            "GMD",
            "GNF",
            "GTQ",
            "GYD",
            "HKD",
            "HNL",
            "HRK",
            "HTG",
            "HUF",
            "IDR",
            "ILS",
            "INR",
            "IQD",
            "IRR",
            "ISK",
            "JMD",
            "JOD",
            "JPY",
            "KES",
            "KGS",
            "KHR",
            "KMF",
            "KPW",
            "KRW",
            "KWD",
            "KYD",
            "KZT",
            "LAK",
            "LBP",
            "LKR",
            "LRD",
            "LSL",
            "LYD",
            "MAD",
            "MDL",
            "MGA",
            "MKD",
            "MMK",
            "MNT",
            "MOP",
            "MRO",
            "MUR",
            "MVR",
            "MWK",
            "MXN",
            "MXV",
            "MYR",
            "MZN",
            "NAD",
            "NGN",
            "NIO",
            "NOK",
            "NPR",
            "NZD",
            "OMR",
            "PAB",
            "PEN",
            "PGK",
            "PHP",
            "PKR",
            "PLN",
            "PYG",
            "QAR",
            "RON",
            "RSD",
            "RUB",
            "RWF",
            "SAR",
            "SBD",
            "SCR",
            "SDG",
            "SEK",
            "SGD",
            "SHP",
            "SLL",
            "SOS",
            "SRD",
            "SSP",
            "STD",
            "SYP",
            "SZL",
            "THB",
            "TJS",
            "TMT",
            "TND",
            "TOP",
            "TRY",
            "TTD",
            "TWD",
            "TZS",
            "UAH",
            "UGX",
            "USD",
            "USN",
            "USS",
            "UYI",
            "UYU",
            "UZS",
            "VEF",
            "VND",
            "VUV",
            "WST",
            "XAF",
            "XAG",
            "XAU",
            "XBA",
            "XBB",
            "XBC",
            "XBD",
            "XCD",
            "XDR",
            "XFU",
            "XOF",
            "XPD",
            "XPF",
            "XPT",
            "XSU",
            "XTS",
            "XUA",
            "YER",
            "ZAR",
            "ZMW"
        );
    }
}

if (!function_exists('search_code_by_country')) {
    function search_code_by_country($search = "")
    {
        $countries = array(
            "afghanistan" => "AF",
            "albania" => "AL",
            "algeria" => "DZ",
            "andorra" => "AD",
            "angola" => "AO",
            "argentina" => "AR",
            "armenia" => "AM",
            "australia" => "AU",
            "austria" => "AT",
            "azerbaijan" => "AZ",
            "bahamas" => "BS",
            "bahrain" => "BH",
            "bangladesh" => "BD",
            "barbados" => "BB",
            "belarus" => "BY",
            "belgium" => "BE",
            "belize" => "BZ",
            "benin" => "BJ",
            "bhutan" => "BT",
            "bolivia" => "BO",
            "bosnia and herzegovina" => "BA",
            "botswana" => "BW",
            "brazil" => "BR",
            "brunei" => "BN",
            "bulgaria" => "BG",
            "burkina faso" => "BF",
            "burundi" => "BI",
            "cambodia" => "KH",
            "cameroon" => "CM",
            "canada" => "CA",
            "cape verde" => "CV",
            "central african republic" => "CF",
            "chad" => "TD",
            "chile" => "CL",
            "china" => "CN",
            "colombia" => "CO",
            "comoros" => "KM",
            "congo (congo-brazzaville)" => "CG",
            "costa rica" => "CR",
            "croatia" => "HR",
            "cuba" => "CU",
            "cyprus" => "CY",
            "czech republic" => "CZ",
            "denmark" => "DK",
            "djibouti" => "DJ",
            "dominica" => "DM",
            "dominican republic" => "DO",
            "ecuador" => "EC",
            "egypt" => "EG",
            "el salvador" => "SV",
            "equatorial guinea" => "GQ",
            "eritrea" => "ER",
            "estonia" => "EE",
            "eswatini" => "SZ",
            "ethiopia" => "ET",
            "fiji" => "FJ",
            "finland" => "FI",
            "france" => "FR",
            "gabon" => "GA",
            "gambia" => "GM",
            "georgia" => "GE",
            "germany" => "DE",
            "ghana" => "GH",
            "greece" => "GR",
            "grenada" => "GD",
            "guatemala" => "GT",
            "guinea" => "GN",
            "guinea-bissau" => "GW",
            "guyana" => "GY",
            "haiti" => "HT",
            "honduras" => "HN",
            "hungary" => "HU",
            "iceland" => "IS",
            "india" => "IN",
            "indonesia" => "ID",
            "iran" => "IR",
            "iraq" => "IQ",
            "ireland" => "IE",
            "israel" => "IL",
            "italy" => "IT",
            "jamaica" => "JM",
            "japan" => "JP",
            "jordan" => "JO",
            "kazakhstan" => "KZ",
            "kenya" => "KE",
            "kiribati" => "KI",
            "korea (north)" => "KP",
            "korea (south)" => "KR",
            "kuwait" => "KW",
            "kyrgyzstan" => "KG",
            "laos" => "LA",
            "latvia" => "LV",
            "lebanon" => "LB",
            "lesotho" => "LS",
            "liberia" => "LR",
            "libya" => "LY",
            "liechtenstein" => "LI",
            "lithuania" => "LT",
            "luxembourg" => "LU",
            "madagascar" => "MG",
            "malawi" => "MW",
            "malaysia" => "MY",
            "maldives" => "MV",
            "mali" => "ML",
            "malta" => "MT",
            "marshall islands" => "MH",
            "mauritania" => "MR",
            "mauritius" => "MU",
            "mexico" => "MX",
            "micronesia" => "FM",
            "moldova" => "MD",
            "monaco" => "MC",
            "mongolia" => "MN",
            "montenegro" => "ME",
            "morocco" => "MA",
            "mozambique" => "MZ",
            "myanmar (burma)" => "MM",
            "namibia" => "NA",
            "nauru" => "NR",
            "nepal" => "NP",
            "netherlands" => "NL",
            "new zealand" => "NZ",
            "nicaragua" => "NI",
            "niger" => "NE",
            "nigeria" => "NG",
            "north macedonia" => "MK",
            "norway" => "NO",
            "oman" => "OM",
            "pakistan" => "PK",
            "palau" => "PW",
            "palestine" => "PS",
            "panama" => "PA",
            "papua new guinea" => "PG",
            "paraguay" => "PY",
            "peru" => "PE",
            "philippines" => "PH",
            "poland" => "PL",
            "portugal" => "PT",
            "qatar" => "QA",
            "romania" => "RO",
            "russia" => "RU",
            "rwanda" => "RW",
            "saint kitts and nevis" => "KN",
            "saint lucia" => "LC",
            "saint vincent and the grenadines" => "VC",
            "samoa" => "WS",
            "san marino" => "SM",
            "saudi arabia" => "SA",
            "senegal" => "SN",
            "serbia" => "RS",
            "seychelles" => "SC",
            "sierra leone" => "SL",
            "singapore" => "SG",
            "slovakia" => "SK",
            "slovenia" => "SI",
            "solomon islands" => "SB",
            "somalia" => "SO",
            "south africa" => "ZA",
            "spain" => "ES",
            "sri lanka" => "LK",
            "sudan" => "SD",
            "suriname" => "SR",
            "sweden" => "SE",
            "switzerland" => "CH",
            "syria" => "SY",
            "taiwan" => "TW",
            "tajikistan" => "TJ",
            "tanzania" => "TZ",
            "thailand" => "TH",
            "timor-leste" => "TL",
            "togo" => "TG",
            "tonga" => "TO",
            "trinidad and tobago" => "TT",
            "tunisia" => "TN",
            "turkey" => "TR",
            "turkmenistan" => "TM",
            "tuvalu" => "TV",
            "uganda" => "UG",
            "ukraine" => "UA",
            "united arab emirates" => "AE",
            "united kingdom" => "GB",
            "united states" => "US",
            "uruguay" => "UY",
            "uzbekistan" => "UZ",
            "vanuatu" => "VU",
            "vatican city" => "VA",
            "venezuela" => "VE",
            "vietnam" => "VN",
            "yemen" => "YE",
            "zambia" => "ZM",
            "zimbabwe" => "ZW"
        );
        $best_match = null;
        $best_score = 0;

        foreach ($countries as $country => $code) {
            similar_text($search, $country, $percentage_score);
            if ($percentage_score > $best_score) {
                $best_score = $percentage_score;
                $best_match = $country;
            }
        }

        return $best_match ? $countries[$best_match] : "CO";
    }
}

if (!function_exists('search_calling_code_by_country')) {
    function search_calling_code_by_country($search = "")
    {
        // Lista de países con sus indicativos
        $countries = array(
            "afghanistan" => "93",
            "albania" => "355",
            "algeria" => "213",
            "andorra" => "376",
            "angola" => "244",
            "argentina" => "54",
            "armenia" => "374",
            "australia" => "61",
            "austria" => "43",
            "azerbaijan" => "994",
            "bahamas" => "1",
            "bahrain" => "973",
            "bangladesh" => "880",
            "barbados" => "1",
            "belarus" => "375",
            "belgium" => "32",
            "belize" => "501",
            "benin" => "229",
            "bhutan" => "975",
            "bolivia" => "591",
            "bosnia and herzegovina" => "387",
            "botswana" => "267",
            "brazil" => "55",
            "brunei" => "673",
            "bulgaria" => "359",
            "burkina faso" => "226",
            "burundi" => "257",
            "cambodia" => "855",
            "cameroon" => "237",
            "canada" => "1",
            "cape verde" => "238",
            "central african republic" => "236",
            "chad" => "235",
            "chile" => "56",
            "china" => "86",
            "colombia" => "57",
            "comoros" => "269",
            "congo (congo-brazzaville)" => "242",
            "costa rica" => "506",
            "croatia" => "385",
            "cuba" => "53",
            "cyprus" => "357",
            "czech republic" => "420",
            "denmark" => "45",
            "djibouti" => "253",
            "dominica" => "1",
            "dominican republic" => "1",
            "ecuador" => "593",
            "egypt" => "20",
            "el salvador" => "503",
            "equatorial guinea" => "240",
            "eritrea" => "291",
            "estonia" => "372",
            "eswatini" => "268",
            "ethiopia" => "251",
            "fiji" => "679",
            "finland" => "358",
            "france" => "33",
            "gabon" => "241",
            "gambia" => "220",
            "georgia" => "995",
            "germany" => "49",
            "ghana" => "233",
            "greece" => "30",
            "grenada" => "1",
            "guatemala" => "502",
            "guinea" => "224",
            "guinea-bissau" => "245",
            "guyana" => "592",
            "haiti" => "509",
            "honduras" => "504",
            "hungary" => "36",
            "iceland" => "354",
            "india" => "91",
            "indonesia" => "62",
            "iran" => "98",
            "iraq" => "964",
            "ireland" => "353",
            "israel" => "972",
            "italy" => "39",
            "jamaica" => "1",
            "japan" => "81",
            "jordan" => "962",
            "kazakhstan" => "7",
            "kenya" => "254",
            "kiribati" => "686",
            "korea (north)" => "850",
            "korea (south)" => "82",
            "kuwait" => "965",
            "kyrgyzstan" => "996",
            "laos" => "856",
            "latvia" => "371",
            "lebanon" => "961",
            "lesotho" => "266",
            "liberia" => "231",
            "libya" => "218",
            "liechtenstein" => "423",
            "lithuania" => "370",
            "luxembourg" => "352",
            "madagascar" => "261",
            "malawi" => "265",
            "malaysia" => "60",
            "maldives" => "960",
            "mali" => "223",
            "malta" => "356",
            "marshall islands" => "692",
            "mauritania" => "222",
            "mauritius" => "230",
            "mexico" => "52",
            "micronesia" => "691",
            "moldova" => "373",
            "monaco" => "377",
            "mongolia" => "976",
            "montenegro" => "382",
            "morocco" => "212",
            "mozambique" => "258",
            "myanmar (burma)" => "95",
            "namibia" => "264",
            "nepal" => "977",
            "netherlands" => "31",
            "new zealand" => "64",
            "nicaragua" => "505",
            "niger" => "227",
            "nigeria" => "234",
            "norway" => "47",
            "oman" => "968",
            "pakistan" => "92",
            "panama" => "507",
            "paraguay" => "595",
            "peru" => "51",
            "philippines" => "63",
            "poland" => "48",
            "portugal" => "351",
            "qatar" => "974",
            "romania" => "40",
            "russia" => "7",
            "rwanda" => "250",
            "saudi arabia" => "966",
            "senegal" => "221",
            "serbia" => "381",
            "seychelles" => "248",
            "sierra leone" => "232",
            "singapore" => "65",
            "slovakia" => "421",
            "slovenia" => "386",
            "somalia" => "252",
            "south africa" => "27",
            "spain" => "34",
            "sri lanka" => "94",
            "sudan" => "249",
            "suriname" => "597",
            "sweden" => "46",
            "switzerland" => "41",
            "taiwan" => "886",
            "tajikistan" => "992",
            "tanzania" => "255",
            "thailand" => "66",
            "tunisia" => "216",
            "turkey" => "90",
            "uganda" => "256",
            "ukraine" => "380",
            "united arab emirates" => "971",
            "united kingdom" => "44",
            "united states" => "1",
            "uruguay" => "598",
            "venezuela" => "58",
            "vietnam" => "84",
            "yemen" => "967",
            "zambia" => "260",
            "zimbabwe" => "263"
        );

        $best_match = null;
        $best_score = 0;

        foreach ($countries as $country => $calling_code) {
            similar_text($search, $country, $percentage_score);
            if ($percentage_score > $best_score) {
                $best_score = $percentage_score;
                $best_match = $country;
            }
        }

        return $best_match ? $countries[$best_match] : "57"; // Devuelve el indicativo de Colombia por defecto
    }
}


/**
 * get dropdown list fro international currency code
 * 
 * @return array
 */
if (!function_exists('get_international_currency_code_dropdown')) {

    function get_international_currency_code_dropdown()
    {
        $result = array();
        foreach (get_international_currency_code_list() as $value) {
            $result[$value] = $value;
        }
        return $result;
    }
}


/**
 * get ignor minor amount 
 * 
 * @return int
 */
if (!function_exists('ignor_minor_value')) {

    function ignor_minor_value($value)
    {
        if (abs($value) < 0.05) {
            $value = 0;
        }
        return $value;
    }
}

if (!function_exists('get_converted_amount')) {

    function get_converted_amount($currency = "", $value = 0)
    {
        if (!$currency) {
            //no currency given
            return $value;
        }

        $conversion_rate = get_setting("conversion_rate");
        $conversion_rate = @unserialize($conversion_rate);
        if (!($conversion_rate && is_array($conversion_rate) && count($conversion_rate))) {
            //no settings found
            return $value;
        }

        $conversion_rate_for_this_currency = get_array_value($conversion_rate, $currency);
        if (!$conversion_rate_for_this_currency) {
            //rate not found for this currency
            return $value;
        }

        //conversion rate found
        return ((1 / $conversion_rate_for_this_currency) * 1) * $value;
    }
}


/**
 * get how much the app should consider to mark an invoice as paid. 
 * 
 * @param number $number
 * @return number
 */
if (!function_exists('get_paid_status_tolarance')) {

    function get_paid_status_tolarance()
    {
        $tolarance = get_setting("paid_status_tolarance");
        if ($tolarance || $tolarance == 0) {
            return $tolarance;
        } else {
            return 0.02;
        }
    }
}
