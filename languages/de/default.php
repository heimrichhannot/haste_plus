<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['MSC']['alias']                                                                                                                      =
    array('Alias', 'Der Alias ist eine eindeutige Referenz, die anstelle der numerischen ID aufgerufen werden kann.');
$GLOBALS['TL_LANG']['MSC']['haste_plus'][\HeimrichHannot\Haste\Dca\General::PROPERTY_AUTHOR_TYPE]                                                        =
    array('Autorentyp', 'Wählen Sie hier den Typ des Autoren aus.');
$GLOBALS['TL_LANG']['MSC']['haste_plus'][\HeimrichHannot\Haste\Dca\General::PROPERTY_AUTHOR_TYPE][\HeimrichHannot\Haste\Dca\General::AUTHOR_TYPE_NONE]   =
    'Kein Autor';
$GLOBALS['TL_LANG']['MSC']['haste_plus'][\HeimrichHannot\Haste\Dca\General::PROPERTY_AUTHOR_TYPE][\HeimrichHannot\Haste\Dca\General::AUTHOR_TYPE_MEMBER] =
    'Mitglied (Frontend)';
$GLOBALS['TL_LANG']['MSC']['haste_plus'][\HeimrichHannot\Haste\Dca\General::PROPERTY_AUTHOR_TYPE][\HeimrichHannot\Haste\Dca\General::AUTHOR_TYPE_USER]   =
    'Benutzer (Backend)';
$GLOBALS['TL_LANG']['MSC']['haste_plus'][\HeimrichHannot\Haste\Dca\General::PROPERTY_AUTHOR]                                                             =
    array('Autor', 'Dieses Feld beinhaltet den Autoren des Datensatzes.');
$GLOBALS['TL_LANG']['MSC']['haste_plus'][\HeimrichHannot\Haste\Dca\General::PROPERTY_SESSION_ID]                                                         =
    array('Session-ID', 'Dieses Feld beinhaltet die Session-ID des berechtigten Bearbeiters.');


/**
 * Date/time
 */
$GLOBALS['TL_LANG']['MSC']['datediff']['just_now']    = 'Gerade eben';
$GLOBALS['TL_LANG']['MSC']['datediff']['min_ago']     = 'Vor 1 Minute';
$GLOBALS['TL_LANG']['MSC']['datediff']['nmins_ago']   = 'Vor %d Minuten';
$GLOBALS['TL_LANG']['MSC']['datediff']['hour_ago']    = 'Vor 1 Stunde';
$GLOBALS['TL_LANG']['MSC']['datediff']['nhours_ago']  = 'Vor %d Stunden';
$GLOBALS['TL_LANG']['MSC']['datediff']['yesterday']   = 'Gestern';
$GLOBALS['TL_LANG']['MSC']['datediff']['ndays_ago']   = 'Vor %d Tagen';
$GLOBALS['TL_LANG']['MSC']['datediff']['week_ago']    = 'Vor 1 Woche';
$GLOBALS['TL_LANG']['MSC']['datediff']['nweeks_ago']  = 'Vor %d Wochen';
$GLOBALS['TL_LANG']['MSC']['datediff']['nmonths_ago'] = 'Vor %d Monaten';
$GLOBALS['TL_LANG']['MSC']['datediff']['year_ago']    = 'Vor %d Jahr';
$GLOBALS['TL_LANG']['MSC']['datediff']['years_ago']   = 'Vor %d Jahren';
$GLOBALS['TL_LANG']['MSC']['second']                  = 'Sekunde';
$GLOBALS['TL_LANG']['MSC']['seconds']                 = 'Sekunden';
$GLOBALS['TL_LANG']['MSC']['minute']                  = 'Minute';
$GLOBALS['TL_LANG']['MSC']['minutes']                 = 'Minuten';
$GLOBALS['TL_LANG']['MSC']['hour']                    = 'Stunde';
$GLOBALS['TL_LANG']['MSC']['hours']                   = 'Stunden';
$GLOBALS['TL_LANG']['MSC']['day']                     = 'Tag';
$GLOBALS['TL_LANG']['MSC']['days']                    = 'Tage';
$GLOBALS['TL_LANG']['MSC']['week']                    = 'Woche';
$GLOBALS['TL_LANG']['MSC']['weeks']                   = 'Wochen';
$GLOBALS['TL_LANG']['MSC']['month']                   = 'Monat';
$GLOBALS['TL_LANG']['MSC']['months']                  = 'Monate';
$GLOBALS['TL_LANG']['MSC']['year']                    = 'Jahr';
$GLOBALS['TL_LANG']['MSC']['years']                   = 'Jahre';
$GLOBALS['TL_LANG']['MSC']['timePeriod']['s']         = 'Sekunde(n)';
$GLOBALS['TL_LANG']['MSC']['timePeriod']['m']         = 'Minute(n)';
$GLOBALS['TL_LANG']['MSC']['timePeriod']['h']         = 'Stunde(n)';
$GLOBALS['TL_LANG']['MSC']['timePeriod']['d']         = 'Tage(n)';

/**
 * Counties
 */
$GLOBALS['TL_LANG']['COUNTIES']['de']['bw'] = 'Baden-Württemberg';
$GLOBALS['TL_LANG']['COUNTIES']['de']['by'] = 'Bayern';
$GLOBALS['TL_LANG']['COUNTIES']['de']['be'] = 'Berlin';
$GLOBALS['TL_LANG']['COUNTIES']['de']['bb'] = 'Brandenburg';
$GLOBALS['TL_LANG']['COUNTIES']['de']['hb'] = 'Bremen';
$GLOBALS['TL_LANG']['COUNTIES']['de']['hh'] = 'Hamburg';
$GLOBALS['TL_LANG']['COUNTIES']['de']['he'] = 'Hessen';
$GLOBALS['TL_LANG']['COUNTIES']['de']['mv'] = 'Mecklenburg-Vorpommern';
$GLOBALS['TL_LANG']['COUNTIES']['de']['ni'] = 'Niedersachsen';
$GLOBALS['TL_LANG']['COUNTIES']['de']['nw'] = 'Nordrhein-Westfalen';
$GLOBALS['TL_LANG']['COUNTIES']['de']['rp'] = 'Rheinland-Pfalz';
$GLOBALS['TL_LANG']['COUNTIES']['de']['sl'] = 'Saarland';
$GLOBALS['TL_LANG']['COUNTIES']['de']['sn'] = 'Sachsen';
$GLOBALS['TL_LANG']['COUNTIES']['de']['st'] = 'Sachsen-Anhalt';
$GLOBALS['TL_LANG']['COUNTIES']['de']['sh'] = 'Schleswig-Holstein';
$GLOBALS['TL_LANG']['COUNTIES']['de']['th'] = 'Thüringen';

/**
 * Operators
 */
$GLOBALS['TL_LANG']['MSC']['operators'] = array(
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_LIKE          => 'enthält',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_UNLIKE        => 'enthält nicht',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_EQUAL         => '=',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_UNEQUAL       => '!=',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_LOWER         => '&lt;',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_GREATER       => '&gt;',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_LOWER_EQUAL   => '&lt;=',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_GREATER_EQUAL => '&gt;=',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_IN            => 'in',
    \HeimrichHannot\Haste\Database\QueryHelper::OPERATOR_NOT_IN        => 'nicht in',
);

$GLOBALS['TL_LANG']['MSC']['connectives'] = array(
    \HeimrichHannot\Haste\Database\QueryHelper::SQL_CONDITION_AND => 'und',
    \HeimrichHannot\Haste\Database\QueryHelper::SQL_CONDITION_OR  => 'oder',
);

/**
 * Misc
 */
$GLOBALS['TL_LANG']['MSC']['yes'] = 'Ja';
$GLOBALS['TL_LANG']['MSC']['no']  = 'Nein';