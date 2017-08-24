<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'prog', language 'sl', branch 'MOODLE_30_STABLE'
 *
 * @package   prog
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activityoverview'] = 'Imate naloge za pregled';
$string['addattempt'] = 'Dovoli dodatno oddajo';
$string['addnewattempt'] = '';
$string['addsubmission'] = 'Oddaj nalogo';
$string['allowsubmissions'] = 'Dovoli uporabnikom oddajo prispevkov v tej nalogi';
$string['allowsubmissionsanddescriptionfromdatesummary'] = 'Podrobnosti naloge in obrazec za oddajo bosta na voljo od <strong>{$a}</strong>';
$string['allowsubmissionsfromdate'] = 'Dovoli začetek oddaje od';
$string['allowsubmissionsfromdate_help'] = 'Če je omogočeno, udeleženci ne bodo mogli oddati nalog pred tem datumom, če pa je onemogočeno, jih lahko oddajo takoj.';
$string['allowsubmissionsfromdatesummary'] = 'Prispevke v nalogi lahko oddate od <strong>{$a}</strong>';
$string['alwaysshowdescription'] = 'Vedno prikaži opis';
$string['alwaysshowdescription_help'] = 'Če je onemogočeno, bo opis dejavnosti viden udeležencem šele na datum začetka oddaje.';
$string['prog:addinstance'] = 'Dodaj novo nalogo';
$string['prog:exportownsubmission'] = 'Izvozi svojo oddajo';
$string['progfeedback'] = 'Vtičnik za odzive';
$string['progfeedbackpluginname'] = 'Vtičnik za odzive';
$string['prog:grade'] = 'Oceni nalogo';
$string['prog:grantextension'] = 'Odobri podaljšanje';
$string['progmentisdue'] = 'Rok za oddajo je potekel';
$string['progmentmail'] = '{$a->grader} je objavil/a odziv na vašo oddano nalogo  \'{$a->progment}\'

Odziv je dodan nalogi:

{$a->url}';
$string['progmentmailhtml'] = '{$a->grader} je objavil odziv na vašo oddano nalogo  \'<i>{$a->progment}</i>\'<br /><br />
Odziv je dodan <a href="{$a->url}">nalogi</a>.';
$string['progmentmailsmall'] = '{$a->grader} je objavil/a odziv na vašo oddano nalogo  \'{$a->progment}\'. Odziv je dodan nalogi.';
$string['progmentname'] = 'Ime naloge';
$string['progmentplugins'] = 'Vtičniki modula Programerska naloga';
$string['progmentsperpage'] = 'Število nalog na stran';
$string['prog:revealidentities'] = 'Razkrij identitete udeležencev';
$string['prog:submit'] = 'Oddaj nalogo';
$string['prog:view'] = 'Ogled naloge';
$string['prog:viewgrades'] = 'Prikaži ocene';
$string['attemptheading'] = 'Attempt {$a->attemptnumber}: {$a->submissionsummary}';
$string['attempthistory'] = 'Prejšnji poskusi';
$string['attemptnumber'] = 'Število oddaj';
$string['attemptreopenmethod'] = 'Omogoči ponovno oddajo';
$string['attemptreopenmethod_help'] = 'Določa možnosti ponovne oddaje naloge za udeležence. Možnosti so: <ul><li>Nikoli - Ponovna oddaja ni mogoča.</li><li>Ročno - izvajalec omogoči študentu ponovno oddajo.</li><li>Dokler ni uspešno opravil - Oddaja je omogočena dokler udeleženec ne doseže zahtevane ocene v redovalnici (področje Kategorije in postavke) za to nalogo.</li></ul>';
$string['attemptreopenmethod_manual'] = 'Ročno';
$string['attemptreopenmethod_none'] = 'Nikoli';
$string['attemptreopenmethod_untilpass'] = 'Samodejno, dokler ni uspešno opravljeno';
$string['attemptsettings'] = 'Nastavitve oddaje';
$string['availability'] = 'Razpoložljivost';
$string['backtoprogment'] = 'Nazaj na nalogo';
$string['batchoperationconfirmaddattempt'] = 'Želite dovoliti ponoven poskus za izbrane oddaje?';
$string['batchoperationconfirmlock'] = 'Želite zakleniti vse izbrane oddaje?';
$string['batchoperationconfirmreverttodraft'] = 'Želite vrniti izbrane oddaje v osnutke?';
$string['batchoperationconfirmunlock'] = 'Želite odkleniti izbrane oddaje?';
$string['batchoperationlock'] = 'Zakleni oddajo';
$string['batchoperationreverttodraft'] = 'Vrni v ponoven pregled';
$string['batchoperationsdescription'] = 'Z izbranimi...';
$string['batchoperationunlock'] = 'Odkleni oddajo';
$string['blindmarking'] = 'Skrij identiteto uporabnika';
$string['blindmarking_help'] = 'Ta nastavitev skrije identiteto udeleženca pred ocenjevalcem. Možnost izbora se onemogoči, ko nekdo od udeležencev odda nalogo.';
$string['choosegradingaction'] = 'Postopek ocenjevanja';
$string['comment'] = 'Komentar';
$string['completionsubmit'] = 'Udeleženec mora predložiti rešitev za zaključek te dejavnosti';
$string['configshowrecentsubmissions'] = 'Vsak lahko vidi obvestila o oddanih nalogah v poročilih o nedavnih dejavnostih.';
$string['confirmsubmission'] = 'Ali želite dokončno oddati vašo nalogo v ocenjevanje? Kasnejše spremembe ne bodo več možne.';
$string['couldnotcreatenewprogmentinstance'] = 'Novega primerka naloge se ne da ustvariti';
$string['currentattempt'] = 'To je vaš {$a} poskus.';
$string['currentattemptof'] = 'To je vaš {$a->attemptnumber}. poskus.
(Število dovoljenih poskusov: {$a->maxattempts})';
$string['currentgrade'] = 'Trenutno ocena v redovalnici';
$string['cutoffdate'] = 'Zadnji možni rok za oddajo';
$string['cutoffdatefromdatevalidation'] = 'Zadnji možni rok za oddajo mora biti za datumom za začetek oddaje.';
$string['cutoffdate_help'] = 'Če je nastavljeno, naloge ne bo mogoče oddati po tem datumu, če ta ne bo podaljšan.';
$string['cutoffdatevalidation'] = 'Zadnji možni rok za oddajo ne more biti pred rokom za oddajo.';
$string['defaultsettings'] = 'Privzete nastavitve za nalogo';
$string['defaultsettings_help'] = 'Te privzete nastavitve veljajo za vse nove naloge.';
$string['defaultteam'] = 'Privzeta skupina';
$string['deleteallsubmissions'] = 'Izbriši vse oddaje';
$string['description'] = 'Opis';
$string['downloadall'] = 'Prenesi vse oddaje';
$string['duedate'] = 'Rok za oddajo';
$string['duedate_help'] = 'Določa rok za oddajo naloge. Po tem datumu je oddaja še vedno možna, a bo naloga označena kot zamujena.
Če želite preprečiti oddajo nalog po določenem datumu, nastavite zadnji možni rok za oddajo.';
$string['duedateno'] = 'Brez roka';
$string['duedatereached'] = 'Rok za oddajo naloge je potekel';
$string['duedatevalidation'] = 'Rok za oddajo mora biti za datumom za začetek oddaje.';
$string['editingstatus'] = 'Stanje urejanja';
$string['editsubmission'] = 'Uredi oddano nalogo';
$string['editsubmission_help'] = 'Popravi oddano nalogo';
$string['enabled'] = 'Omogočeno';
$string['errornosubmissions'] = 'Ni oddaj za prenos';
$string['errorquickgradingvsadvancedgrading'] = 'Ocene niso bile shranjene, ker je za nalogo nastavljeno napredno ocenjevanje.';
$string['eventallsubmissionsdownloaded'] = 'Vse oddaje se prenašajo';
$string['eventsubmissionlocked'] = 'Oddaje so za udeleženca zaklenjene';
$string['eventsubmissionunlocked'] = 'Oddaje so za udeleženca odklenjene';
$string['extensionduedate'] = 'Podaljšanje zapadlosti';
$string['extensionnotafterfromdate'] = 'Rok za podaljšanje mora biti po datumu za začetek oddaje.';
$string['feedback'] = 'Odziv';
$string['feedbackavailablehtml'] = '{$a->username} je objavil/a odziv na vašo oddano nalogo \'<i>{$a->progment}</i>\'<br /><br />
Odziv je dodan <a href="{$a->url}">nalogi</a>.';
$string['feedbackavailablesmall'] = '{$a->username} se je odzval/a na vašo nalogo {$a->progement}';
$string['feedbackavailabletext'] = '{$a->username} je objavil/a odziv na vašo oddano nalogo \'{$a->progement}\'.

Odziv je odan nalogi:

{$a->url}';
$string['feedbackplugin'] = 'Vtičnik za odzive';
$string['feedbackpluginforgradebook'] = 'Vtičnik za odzive, ki potisne komentarje v redovalnico';
$string['feedbackplugins'] = 'Vtičniki za odzive';
$string['feedbacksettings'] = 'Nastavitve odzivov';
$string['feedbacktypes'] = 'Vrste odzivov';
$string['filesubmissions'] = 'Oddaja datotek';
$string['filternone'] = 'Brez';
$string['filterrequiregrading'] = 'Potrebuje ocenjevanje';
$string['filtersubmitted'] = 'Oddano';
$string['gradeabovemaximum'] = 'Ocena mora biti nižja eli enaka {$a}.';
$string['gradebelowzero'] = 'Ocena mora biti višja ali enaka nič.';
$string['gradecanbechanged'] = 'Ocena se lahko spremeni.';
$string['graded'] = 'Ocenjeno';
$string['gradedby'] = 'Ocenil';
$string['gradedon'] = 'Ocenjeno v';
$string['gradeoutof'] = 'Ocena od {$a}';
$string['gradeoutofhelp'] = 'Ocena';
$string['gradeoutofhelp_help'] = 'Ocenite nalogo udeleženca. Lahko uporabite decimalke.';
$string['gradersubmissionupdatedhtml'] = '{$a->username} je posodobil/a svojo oddano nalogo
<i>\'{$a->progment}\'</i><br /><br />
Oddana naloga <a href="{$a->url}">je na voljo na spletni strani</a>.';
$string['gradersubmissionupdatedsmall'] = '{$a->username} je posodobil/a svojo oddano nalogo {$a->progement}';
$string['gradersubmissionupdatedtext'] = '{$a->username} je posodobil/a svojo oddano nalogo \'{$a->progment}\' ob {$a->timeupdated}

Na voljo je tu:

    {$a->url}';
$string['gradestudent'] = 'Oceni udeleženca: (id={$a->id}, fullname={$a->fullname}).';
$string['gradeuser'] = 'Oceni {$a}';
$string['grading'] = 'Ocenjevanje';
$string['gradingchangessaved'] = 'Spremembe ocen so bile shranjene';
$string['gradingmethodpreview'] = 'Ocenjevalni kriteriji';
$string['gradingoptions'] = 'Možnosti';
$string['gradingstatus'] = 'Stanje ocen';
$string['gradingstudent'] = 'Ocenjevanje udeleženca';
$string['gradingsummary'] = 'Povzetek ocenjevanja';
$string['grantextension'] = 'Odobri podaljšanje';
$string['grantextensionforusers'] = 'Odobri podaljšanje za {$a} udeležencev.';
$string['groupsubmissionsettings'] = 'Nastavitve oddaje za skupino';
$string['hiddenuser'] = 'Sodelujoči';
$string['hideshow'] = 'Skrij/Prikaži';
$string['introattachments'] = 'Dodatne datoteke';
$string['introattachments_help'] = 'Lahko naložite dodatne datoteke, ko so potrebne za izdelavo naloge (npr. predloge za odgovore). Povezave za prenos teh datotek bodo prikazane na strani naloge pod opisom.';
$string['lastmodifiedgrade'] = 'Zadnja sprememba (ocene)';
$string['lastmodifiedsubmission'] = 'Zadnja sprememba (oddaje)';
$string['latesubmissions'] = 'Zamujene oddaje';
$string['latesubmissionsaccepted'] = 'Le udeleženci, ki jim je bil podaljšan čas, lahko oddajo nalogo';
$string['locksubmissions'] = 'Zakleni oddajo';
$string['maxattempts'] = 'Največje število oddaj';
$string['maxattempts_help'] = 'Največje število poskusov oddaj, ki jih lahko opravi udeleženec. Ko je ta številka poskusov dosežena udeleženec ne more več ponovno oddati naloge.';
$string['maxgrade'] = 'Najvišja ocena';
$string['modulename'] = 'Programerska naloga';
$string['modulename_help'] = 'Programerska naloga omogoča izvajalcu, da udležencem  dodeli zadolžitve/domače naloge, jih zbere, oceni in poda povrtno informacijo.

Udeleženci lahko oddajo nalogo v katerikoli elektronski obliki, npr.: besedilno datoteko, preglednico, sliko, avdio/video posnetek. Naloga lahko zahteva tudi, da udeleženci vnesejo besedilo neposredno v obrazec za oddajo. Nalogo lahko oddajo skupinsko ali pa kot samostojen izdelek.

Pri pregledovanju in ocenjevanju naloge, lahko izvajalec poda komentarje tako, da naloži popravljeno nalogo kot dokument, lahko naloži avdio posnetek s komentarji ipd.

Ocena naloge je lahko v številčni ali drugi določeni obliki (npr. opisno). Končna ocena je zabeležena v redovalnici.';
$string['modulenameplural'] = 'Programerske naloge';
$string['newsubmissions'] = 'Oddane naloge';
$string['noattempt'] = 'Neoddano';
$string['nofiles'] = 'Ni datotek.';
$string['nograde'] = 'Brez ocene';
$string['noonlinesubmissions'] = 'Pri tej nalogi vam ni treba oddati ničesar.';
$string['nosavebutnext'] = 'Naslednje';
$string['nosubmission'] = 'V tej nalogi še ni oddanih prispevkov';
$string['nosubmissionsacceptedafter'] = 'Naloge ne morete oddati po';
$string['notgraded'] = 'Neocenjeno';
$string['notgradedyet'] = 'Še neocenjeno';
$string['notifications'] = 'Obvestila';
$string['notsubmittedyet'] = 'Še ne oddano';
$string['nousersselected'] = 'Izbran ni bil noben uporabnik';
$string['numberofdraftsubmissions'] = 'Osnutki';
$string['numberofparticipants'] = 'Udeleženci';
$string['numberofsubmissionsneedgrading'] = 'Čaka na ocenjevanje';
$string['numberofsubmittedprogments'] = 'Oddanih';
$string['numberofteams'] = 'Skupine';
$string['outlinegrade'] = 'Ocena: {$a}';
$string['outof'] = '{$a->current} od {$a->total}';
$string['overdue'] = '<font color="red">Rok za oddajo je potekel pred: {$a}</font>';
$string['page-mod-prog-view'] = 'Glavna stran modula nalog';
$string['page-mod-prog-x'] = 'Vsaka stran modula nalog';
$string['participant'] = 'Sodelujoči';
$string['pluginadministration'] = 'Administracija modula Programerska naloga';
$string['pluginname'] = 'Programerska naloga';
$string['preventsubmissions'] = 'Prepreči udeležencu oddajo naloge';
$string['preventsubmissionsshort'] = 'Prepreči spreminjanje oddanih nalog';
$string['previous'] = 'Prejšnje';
$string['quickgrading'] = 'Hitro ocenjevanje';
$string['quickgradingchangessaved'] = 'Spremembe ocen so bile shranjene';
$string['quickgrading_help'] = 'Hitro ocenjevanje omogoča dodelitev ocen neposredno v tabelo oddanih nalog (zgoraj). Hitro ocenjevanje ni združljivo z naprednim ocenjevanjem in ni priporočljivo, če ocenjuje več ocenjevalcev.';
$string['quickgradingresult'] = 'Hitro ocenjevanje';
$string['requireallteammemberssubmit'] = 'Vsi člani skupine morajo oddati';
$string['requireallteammemberssubmit_help'] = 'Če je omogočeno, mora vsk član skupini klikniti na gumb "Oddaj", preden je skupinska naloga oddana.
Če je onemogočeno, bo skupinska naloga oddana, ko kateri koli član skupine klikne na gumb "Oddaj".';
$string['requiresubmissionstatement'] = 'Udeleženec mora potrditi izjavo o izvirnosti svojih prispevkov';
$string['requiresubmissionstatement_help'] = 'Udeleženec mora potrditi izjavo o izvirnosti vseh prispevkov v tej nalogi';
$string['revealidentities'] = 'Razkrij identitete udeležencev';
$string['revealidentitiesconfirm'] = 'Ali ste prepričani, da želite prikazati identiteto udeleženca za to nalogo? Tega potem ni več mogoče razveljaviti. Ko je identiteta udeleženca odkrita bodo oznake prikazane v redovalnici.';
$string['reverttodraft'] = 'Povrni v status osnutka';
$string['reverttodraftforstudent'] = 'Nalogo vrni v ponoven pregled udeležencu: (id={a->id}, fullname={a->fullname})';
$string['reverttodraftshort'] = 'Povrni v status osnutka';
$string['reviewed'] = 'Pregledano';
$string['saveallquickgradingchanges'] = 'Shrani spremembe hitrega ocenjevanja';
$string['savechanges'] = 'Shrani spremembe';
$string['savegradingresult'] = 'Oceni';
$string['savenext'] = 'Shrani in prikaži naslednjo';
$string['scale'] = 'Lestvica';
$string['selectedusers'] = 'Izbrani uporabniki';
$string['selectlink'] = 'Izberite...';
$string['selectuser'] = 'Izberite {$a}';
$string['sendlatenotifications'] = 'Obvesti ocenjevalce o zamujenih oddajah';
$string['sendlatenotifications_help'] = 'Če je omogočeno, ocenjevalci (običajno izvajalci) prejmejo sporočilo, ko udeleženec zamudi z oddajo. Metode obveščanja je možno nastaviti.';
$string['sendnotifications'] = 'Obvesti ocenjevalce o oddajah';
$string['sendnotifications_help'] = 'Če je omogočeno, ocenjevalci (običajno izvajalci) prejmejo sporočilo, ko udeleženec oddajo zamudi, jo pošlje pravočasno ali je zgoden. Metode obveščanja je možno nastaviti.';
$string['sendstudentnotifications'] = 'Obvesti udeležence';
$string['sendstudentnotificationsdefault'] = 'Privzeta nastavitev za "Obvesti udeležence"';
$string['sendstudentnotifications_help'] = 'Če je omogočeno, prejmejo udeleženci sporočilo o posodobitvi ocene ali odzivu.';
$string['sendsubmissionreceipts'] = 'Pošlji udeležencem potrdilo o oddaji';
$string['sendsubmissionreceipts_help'] = 'To stikalo omogoči pošiljanje obvestil o oddaji udeležencem, vsakič ko nalogo uspešno oddajo.';
$string['settings'] = 'Nastavitve modula Programerska naloga';
$string['showrecentsubmissions'] = 'Prikaži nedavno oddane naloge';
$string['status'] = 'Stanje';
$string['submission'] = 'Oddaja';
$string['submissiondrafts'] = 'Udeleženec naj klikne gumb "Oddaj"';
$string['submissiondrafts_help'] = 'Če je omogočeno,  morajo udeleženci klikniti na gumb "Oddaj" za dokončno oddajo naloge. To jim omogoča, da obdržijo osnutek naloge v sistemu. Če nastavitev spremenite iz "Ne" v "Da", potem ko so no naloge že oddali, bo oddaja veljala za dokončno.';
$string['submissioneditable'] = 'Udeleženec lahko ureja oddano nalogo';
$string['submissionnoteditable'] = 'Udeleženec ne more urejati oddane naloge';
$string['submissionplugins'] = 'Vtičniki oddaje';
$string['submissions'] = 'Oddaje';
$string['submissionsclosed'] = 'Oddajanje je zaprto';
$string['submissionsettings'] = 'Nastavitve oddaje';
$string['submissionsnotgraded'] = 'Št. neocenjenih oddanih nalog: {$a}';
$string['submissionstatement'] = 'Izjava o izvirnosti';
$string['submissionstatementacceptedlog'] = 'Udeleženec {a$} je potrdil izjavo o izvirnosti';
$string['submissionstatementdefault'] = 'Ta naloga je moje lastno delo, razen kjer citiram in navajam dela drugih avtorjev.';
$string['submissionstatus'] = 'Status oddaje naloge';
$string['submissionstatus_'] = 'Ni oddaje';
$string['submissionstatus_draft'] = 'Predloga (neoodano)';
$string['submissionstatusheading'] = 'Status oddaje naloge';
$string['submissionstatus_marked'] = 'Ocenjeno';
$string['submissionstatus_new'] = 'Ni oddaje';
$string['submissionstatus_reopened'] = 'Ponovna oddaja omogočena';
$string['submissionstatus_submitted'] = 'Oddano v ocenjevanje';
$string['submissionsummary'] = '{$a->status}. Zadnja sprememba {$a->timemodified}';
$string['submissionteam'] = 'Skupina';
$string['submissiontypes'] = 'Vrste oddanih nalog';
$string['submitprogment'] = 'Oddaj nalogo';
$string['submitprogment_help'] = 'Ko boste dokončno oddali nalogo, je ne boste mogli več popraviti.';
$string['submitted'] = 'Oddano';
$string['submittedearly'] = 'Naloga je bila oddana {$a} prezgodaj';
$string['submittedlate'] = 'Naloga je bila oddana {$a} prepozno';
$string['submittedlateshort'] = '{$a} prepozno';
$string['subplugintype_progfeedback'] = 'Vtičnik za odzive';
$string['subplugintype_progfeedback_plural'] = 'Vtičniki za odzive';
$string['subplugintype_progsubmission_plural'] = 'Vtičniki oddaje';
$string['teamsubmission'] = 'Udeleženci oddajo skupinsko';
$string['teamsubmissiongroupingid'] = 'Skupki skupin udeležencev';
$string['teamsubmissiongroupingid_help'] = 'Ta skupek bo uporabljen za skupine udeležencev, določenih v nalogi. Če ni nastavljeno, bo uporabljenaprivzeta skupina.';
$string['teamsubmission_help'] = 'Če je omogočeno, bodo udeleženci razdeljeni v skupine, glede na prednastavitve skupin oz. skupkov. Skupinska oddaja je dostopna članom skupine, ki tudi vidijo spremembe, vnešene s strani posameznih članov skupine.';
$string['textinstructions'] = 'Navodila naloge';
$string['timemodified'] = 'Zadnja sprememba';
$string['timeremaining'] = 'Preostali čas';
$string['unlimitedattempts'] = 'Neomejeno';
$string['unlimitedattemptsallowed'] = 'Neomejeno število poskusov.';
$string['unlocksubmissionforstudent'] = 'Dovoli oddajo udeležencu: (id={$a->id}, fullname={$a->fullname}).';
$string['unlocksubmissions'] = 'Odkleni oddajo';
$string['updategrade'] = 'Posodobi oceno';
$string['viewgradebook'] = 'Poglej redovalnico';
$string['viewgrading'] = 'Preglej/oceni vse oddane naloge';
$string['viewownsubmissionstatus'] = 'Poglej status svojih oddaj';
$string['viewrevealidentitiesconfirm'] = 'Poglej stran za potrditev razkritja identitete udeležencev.';
$string['viewsubmissiongradingtable'] = 'Ogled ocenjevalne lestvice';
