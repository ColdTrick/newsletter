<?php

return array(
	// general
	'item:object:newsletter' => "Newsletter",
	'item:object:newsletter_template' => "Newsletter Template",
	'item:object:newsletter_subscription' => "Newsletter-Anmeldung",
	'newsletter:add' => "Neuer Newsletter",
	'newsletter:subscribe' => "Abonnieren",
	'newsletter:unsubscribe' => "Abmelden",
	'newsletter:duplicate_of' => "Duplizieren von",
	
	'newsletter:status:concept' => "Konzept",
	'newsletter:status:sending' => "Derzeit versendet",
	'newsletter:status:scheduled' => "Geplant",
	'newsletter:status:sent' => "Gesendet",
	
	'newsletter:cli:error:secret' => "Ungültiges Secret für den Einsatz des CLI",
	
	// CSV upload
	'newsletter:csv:no_email' => "In den ersten 2 Zeilen der CSV-Datei konnte keine E-Mail-Spalte gefunden werden",
	'newsletter:csv:added' => "Es wurden %s E-Mail-Adressen aus der the CSV-Datei hinzugefügt",
	
	// menu's
	'newsletter:menu:site' => "Newsletter",
	
	'newsletter:menu:page:subscriptions' => "Meine Abonnement-Einstellungen",
	'newsletter:menu:page:settings' => "Newsletter-Abonnements",
	'newsletter:menu:page:received' => "Meine erhaltenen Newsletter",
	
	'newsletter:menu:entity:log' => "Log anzeigen",
	'newsletter:menu:entity:duplicate' => "Duplizieren",
	
	'newsletter:menu:owner_block:group' => "Gruppen Newsletter",
	
	'newsletter:menu:filter:sent' => "Gesendet",
	'newsletter:menu:filter:concept' => "Konzept",
	'newsletter:menu:filter:scheduled' => "Geplant",
	'newsletter:menu:filter:sending' => "Derzeit gesendet",
	
	// steps menu
	'newsletter:menu:steps:entity' => "Grundeinstellungen",
	'newsletter:menu:steps:content' => "Inhalt",
	'newsletter:menu:steps:template' => "Template",
	'newsletter:menu:steps:recipients' => "Empfänger",
	'newsletter:menu:steps:schedule' => "Zeitplan",
		
	// long text menu
	'newsletter:menu:longtext:embed_content' => "Inhalte einbinden",
	
	'newsletter:breadcrumb:site' => "Newsletter",
	'newsletter:breadcrumb:log' => "Lieferprotokoll",
	'newsletter:breadcrumb:received' => "Empfangen",
	
	// pages
	'newsletter:site:title' => "Alle Website-Newsletter",
	'newsletter:add:title' => "Erstelle einen Newsletter",
	'newsletter:edit:title' => "Newsletter bearbeiten: %s",
	'newsletter:schedule:title' => "%s: Zeitplan",
	'newsletter:received:title' => "%s's erhaltene Newsletter",
	'newsletter:received:title:mine' => "Meine erhaltenen Newsletter",
	
	// embed
	'newsletter:embed:show_all' => "Zeige alle Inhalte, nicht nur von Gruppen",
	'newsletter:embed:format:description:title' => "Beschreibung anzeigen",
	'newsletter:embed:format:description:option:full' => "Vollständig",
	'newsletter:embed:format:description:option:excerpt' => "Auszug",
	'newsletter:embed:format:description:option:no' => "Nein",

	'newsletter:embed:format:icon:title' => "Symbol anzeigen",
	'newsletter:embed:format:icon:option:left' => "Linksbündig",
	'newsletter:embed:format:icon:option:right' => "Rechtsbündig",
	'newsletter:embed:format:icon:option:none' => "Symbol nicht anzeigen",
	
	'newsletter:embed:format:add_to_newsletter' => "Zum Newsletter hinzufügen",
	'newsletter:embed:format:preview:title' => "Vorschau",
	
	'newsletter:embed:read_more' => "Weiterlesen",
	
	// edit
	'newsletter:edit:subject' => "Benutzerdefinierter E-Mail-Betreff (optional)",
	'newsletter:edit:from' => "Benutzerdefinierter Absender-E-Mail-Adresse (optional)",
	'newsletter:edit:from:description' => "Der Newsletter wird standardmäßig über %s verschickt. Hier kannst Du eine benutzerdefinierte E-Mail-Adresse eingeben.Du erhältst Unzustellbarkeitsberichte an diese E-Mail-Adresse.",
	'newsletter:edit:description:description' => "Diese Beschreibung wird in der Auflistung der Newsletter verwendet und ist nicht Teil des Newsletters selbst.",
	
	// placeholders
	'newsletter:placeholders:info' => "Du kannst die folgenden Platzhalter in Deinem Text verwenden. Diese werden durch Systemtext oder durch Informationen über den Newsletter ersetzt. Bei Mouse-Over erhältst Du einige Details.",
	'newsletter:placeholders:content' => "Content wird im Schritt Inhalte des Newsletters ersetzt (nicht auf der Inhaltsseite verwenden)",
	'newsletter:placeholders:unsub' => "Unsub wird durch einen Abmelden-Text ersetzt, der einen Abmelden-Link enthält",
	'newsletter:placeholders:unsublink' => "Unsublink wird durch einen Abmelden-Link ersetzt",
	'newsletter:placeholders:online' => "Online wird durch einen Standardtext ersetzt, wo der Newsletter online zu sehen ist",
	'newsletter:placeholders:title' => "Title wird durch den Titel des Newsletters ersetzt",
	'newsletter:placeholders:description' => "Beschreibung wird durch den Beschreibungstext des Newsletters ersetzt",
	'newsletter:placeholders:subject' => "Subject wird durch den Betreff des Newsletters ersetzt",
	'newsletter:placeholders:newsletter_url' => "Newsletter_url wird durch die URL zum Newsletter ersetzt",
	'newsletter:placeholders:site_name' => "Site_name wird durch den Namen der Website ersetzt",
	'newsletter:placeholders:site_description' => "Site_description wird durch die Beschreibung der Website ersetzt",
	'newsletter:placeholders:site_url' => "Site_url wird durch die URL der Website ersetzt",
	'newsletter:placeholders:container_name' => "Container_name wird durch den Namen des Containers ersetzt (kann eine Gruppe oder die Website sein)",
	'newsletter:placeholders:container_url' => "Container_url wird durch die URL des Containers ersetzt (kann eine Gruppe oder die Website sein)",
		
	// content
	'newsletter:edit:content:description' => "Füge hier Deinem Newsletter Inhalte hinzu. Erstelle einen freien Text oder importiere einen vorhandenen Blog-Beitrag als Inhalt in Deinen Newsletter.",
				
	// template
	'newsletter:edit:template:description' => "Hier kannst Du Newsletter-Layout kontrollieren, indem Du den HTML-Code änderst oder das Styling über das CSS änderst. Vergiss nicht, Dir eine Vorschau anzusehen, um zu prüfen, ob der Newsletter wie erwartet aussieht.",
	'newsletter:edit:template:copy_to_custom' => "Kopiere zu Benutzerdefiniert",
	'newsletter:edit:template:copy_to_custom:confirm' => "Diese Aktion überschreibt die aktuelle benutzerdefinierte Vorlage. Bist Du sicher, dass Du diese Vorlage in eine benutzerdefinierte Vorlage für diesen Newsletter kopieren möchtest?",
	'newsletter:edit:template:select' => "Wähle eine Vorlage",
	'newsletter:edit:template:select:default' => "Standardvorlage",
	'newsletter:edit:template:select:default2' => "Standardvorlage (Mit Seitenleiste)",
	'newsletter:edit:template:select:custom' => "Benutzerdefinierte Vorlage",
	'newsletter:edit:template:html' => "HTML",
	'newsletter:edit:template:css' => "CSS",
	'newsletter:edit:template:name' => "Name für die benutzerdefinierte Vorlage (nur für das Speichern der Vorlage erforderlich)",
	'newsletter:edit:template:save_as' => "Als Vorlage speichern",
	'newsletter:edit:template:error:save_as' => "Bitte prüfe die markierten Felder",
		
	// default template body
	'newsletter:body:unsub' => "Dieser Newsletter wird gesendet von <a href='{container_url}' rel='nofollow'>{container_name}</a>. Klicke <a href='{unsublink}'>hier</a> um Dich von diesem Newsletter abzumelden.",
	'newsletter:body:online' => "Wenn Sie diesen Newsletter nicht lesen können, überprüfen Sie ihn <a href='{newsletter_url}'>online</a>",
		

	// schedule
	'newsletter:schedule:description' => "Hier kannst Du festlegen, wann der Newsletter an die ausgewählten Empfänger ausgeliefert wird.",
	'newsletter:schedule:date' => "Geplantes Datum",
	'newsletter:schedule:time' => "Geplante Zeit",
	'newsletter:schedule:status_notification' => "E-Mail-Adresse für Statusmeldung",
	'newsletter:schedule:status_notification:description' => "Wenn der Newsletter versendet wurde, wird eine Benachrichtigung an diese E-Mail-Adresse gesendet.",
	'newsletter:schedule:status_notification:me' => "Schicke mir eine Statusmeldung",
	'newsletter:schedule:status_notification:toggle' => "oder gib eine benutzerdefinierte E-Mail-Adresse ein",
	'newsletter:schedule:show_in_archive' => "Den Newsletter im Archiv anzeigen",
	'newsletter:schedule:send' => "Speichern und jetzt senden",
	'newsletter:schedule:save' => "Speichern und Versand planen",
	'newsletter:schedule:no_recipients' => "Sie haben keine Empfänger ausgewählt. Bist Du sicher, dass Du diese Aktion durchführen möchtest?",

	// recipients
	'newsletter:recipients:title' => "Empfänger auswählen",
	'newsletter:recipients:tooltip' => "Alle Mitglieder anschreiben? Dann schickst du den Newsletter an alle Mitglieder der Gruppe (außer denen, die darauf hingewiesen haben, dass sie den Newsletter nicht erhalten möchten). Mit allen Abonnenten sind alle Abonnenten gemeint, die nicht notwendige Mitglieder Deiner Gruppe sind, aber die ein Abonnement für den Newsletter haben. Wenn die Nummer hinter Abonnenten eine (0) ist, dann hat dieser Newsletter keine Abonnenten.",
	'newsletter:recipients:description' => "Nachfolgend kannst Du konfigurieren, an wen Du den Newsletter versenden möchtest.",
	'newsletter:recipients:csv' => "Lade eine CSV-Datei mit E-Mail-Adressen der Empfänger hoch",
	'newsletter:recipients:csv:description' => "Du kannst eine CSV-Datei hochladen, die die E-Mail-Adressen der Empfänger enthält. Der Textbegrenzer für die Datei muss \" (doppeltes Zitat) sein und der Spaltenbegrenzer muss ein ; (Semikolon) sein. Das System erkennt die E-Mail-Spalte automatisch, indem die ersten 2 Zeilen durchsucht werden.",
	'newsletter:recipients:recipient' => "Einen Empfänger suchen",
	'newsletter:recipients:recipient:description' => "Sie können nach Namen, E-Mail und Benutzernamen suchen. Bitte wählen Sie aus dem Dropdown-Menü, um den Empfänger hinzuzufügen.",
	'newsletter:recipients' => "Empfänger",
	'newsletter:recipients:subscribers' => "Für alle Abonnenten",
	'newsletter:recipients:members' => "Für alle Mitglieder",
	'newsletter:recipients:members:site' => "Mitglieder der Site",
	'newsletter:recipients:members:group' => "Gruppenmitglieder",
	'newsletter:recipients:email' => "E-Mail-Addresse",
	
	// plugin settings
	'newsletter:settings:allow_groups' => "Gruppen-Admins erlauben, Newsletters zu versenden",
	'newsletter:settings:allow_groups:description' => "Gruppen-Administratoren können einen Newsletter für ihre Gruppenmitglieder erstellen.",
	'newsletter:settings:include_existing_users' => "Benutzer ohne Abonnementeinstellungen einschließen",
	'newsletter:settings:include_existing_users:description' => "Wenn diese Einstellung auf 'nein' gesetzt ist, erhalten alle vorhandenen Benutzer ohne Abonnement keine Newsletter mehr.",
	'newsletter:settings:custom_from' => "Erlaube, dass die Newsletter von einer benutzerdefinierten E-Mail-Adresse gesendet werden",
	'newsletter:settings:custom_from:description' => "Wenn diese Einstellung auf 'ja' gesetzt ist, können Benutzer eine benutzerdefinierte E-Mail-Adresse eingeben, die als Absenderadresse des Newsleters verwendet wird. Bitte beachte, dass dies Missbrauch ermöglicht und der Newsletter häufiger in Spam-Ordnern landen könnte.",
	'newsletter:settings:custom_from:domains' => "Beschränken Sie die benutzerdefinierten E-Mail-Adressen auf die folgenden Domains",
	'newsletter:settings:custom_from:domains:description' => "Geben Sie eine durch Kommas getrennte Liste von Domains ein, um die benutzerdefinierten E-Mail-Adressen zu beschränken. Zum Beispiel: example.com erlaubt user@example.com, aber nicht user@example2.com oder user2@sub.example.com",
	
	'newsletter:settings:url_postfix' => "URL-Postfix-Einstellungen",
	'newsletter:settings:url_postfix:description' => "Sie können einen oder mehrere Postfix-Name/Wert-Paare konfigurieren, die in allen (internen) Links platziert werden, die im Newsletter ausgehen. Sie können im Feld 'name=value' (z.B. source=newsletter) ein Name/Wertpaar pro Zeile pro Zeile konfigurieren. Dies ermöglicht es Tracking-Systemen zu erkennen, ob Benutzer vom Newsletter kamen.",
	'newsletter:settings:url_postfix:setting' => "Postfix-Einstellungen",
	'newsletter:settings:url_postfix:setting:description' => "Es gibt bestimmte Platzhalter, die du in deinen Postfixwerten verwenden kannst: %s",
	
	// entity view
	'newsletter:entity:scheduled' => "Geplant",
	'newsletter:entity:sent' => "Gesendet",
	'newsletter:entity:error:code' => "Ungültiger oder fehlender Code, um diesen Newsletter online zu sehen",
	
	// my subscriptions
	'newsletter:subscriptions:description' => "Hier können Sie alle Ihre Newsletter-Abonnements verwalten.",
	'newsletter:subscriptions:site:title' => "Site-Newsletter",
	'newsletter:subscriptions:site:description' => "Möchten Sie einen Newsletter von der Community erhalten.",
	'newsletter:subscriptions:groups:title' => "Meine Gruppen-Newsletter",
	'newsletter:subscriptions:groups:description' => "Alle Gruppen, bei denen Du Mitglied bist, sind aufgeführt, so dass Du Deine Newsletter-Abonnements leicht ändern kannst.",
	'newsletter:subscriptions:other:title' => "Anderes Abonnement",
	'newsletter:subscriptions:other:description' => "Möchtest Du einen Newsletter von einer Gruppe erhalten, bei der Du nicht Mitglied bist? Sie sind nachfolgend aufgelistet.",
	
	// unsubscribe
	'newsletter:unsubscribe:error:input' => "Falsche Eingabe, bitte überprüfe den Link in Deiner E-Mail",
	'newsletter:unsubscribe:error:code' => "Ungültiger Abbestellungscode, bitte überprüfe den Link in Deiner E-Mail",
	'newsletter:unsubscribe:error:invalid_user' => "Der Abmelden-Link ist für Dein Benutzerkonto nicht gültig",
	'newsletter:unsubscribe:title' => "Newsletter abbestellen",
	'newsletter:unsubscribe:user' => "Hallo %s,

Überprüfen die beiden nachfolgenden Einstellungen und klicke auf Abmelden, um den Vorgang abzuschließen.",
	'newsletter:unsubscribe:email' => "Deine E-Mail-Adresse %s wird abbestellt, wenn Du auf den nachfolgenden Button klickst.",
	'newsletter:unsubscribe:email:empty' => "Gib Deine E-Mail-Adresse ein und klicke auf den nachfolgenden Button um Dich abzumelden.",
	'newsletter:unsubscribe:entity' => "Ich möchte nicht mehr den '%s' Newsletter erhalten",
	'newsletter:unsubscribe:all' => "Ich möchte keine Newsletter von der %s Community erhalten",
	
	// sidebar - subscribe
	'newsletter:sidebar:subscribe:title' => "Newsletter abonnieren",
	'newsletter:subscribe:email:description' => "%s Newsletter abonnieren",
	'newsletter:subscribe:user:description:subscribe' => "%s Newsletter abonnieren",
	'newsletter:subscribe:user:description:unsubscribe' => "%s Newsletter abmelden",
	
	// registration
	'newsletter:registration' => "Ich möchte den Site-Newsletter erhalten",
	
	// email content
	'newsletter:subject' => "%s Newsletter: %s",
	'newsletter:plain_message' => "Um den Newsletter korrekt zu sehen, muss Dein E-Mail-Client HTML-Mails unterstützen.

Um den Newsletter online zu sehen, klicke hier:
%s",
	
	// status notification
	'newsletter:status_notification:subject' => "Newsletter-Statusbenachrichtigung",
	'newsletter:status_notification:message' => "LS,

Dein Newsletter '%s' wurde versendet.

Um den Newsletter zu sehen, klicke hier:
%s",
	
	// logging
	'newsletter:log:title' => "Delivery log: %s",
	'newsletter:log:counter:success' => "Gesendet",
	'newsletter:log:counter:error' => "Fehler",
	
	'newsletter:log:users:title' => "Benutzer",
	'newsletter:log:users:header:email' => "E-Mail Adresse",
	'newsletter:log:users:header:time' => "Zeit",
	'newsletter:log:users:header:status' => "Status",
	'newsletter:log:users:header:guid' => "Benutzer",
	
	'newsletter:log:emails:title' => "E-mail addresses",
	'newsletter:log:email:header:email' => "E-Mail Adresse",
	'newsletter:log:email:header:time' => "Zeit",
	'newsletter:log:email:header:status' => "Status",
	
	'newsletter:log:general:title' => "Allgemeine Information",
	'newsletter:log:general:scheduled' => "Geplante Zeit",
	'newsletter:log:general:starttime' => "Tatsächliche Startzeit",
	'newsletter:log:general:endtime' => "Bearbeitung beendet",
	
	'newsletter:log:no_contents' => "Es konnte keine Protokolldatei gefunden werden, sind Sie sicher, dass dieser Newsletter bereits gesendet wurde?",
	'newsletter:log:no_recipients' => "Es gab keine Empfänger für diesen Newsletter",
	'newsletter:log:emails:no_recipients' => "Bei der Abwicklung des Newsletters gab es keine individuellen E-Mail-Adressen. Wenn Sie einige E-Mail-Adressen ausgewählt hätten, könnten sie sich vom Newsletter abgemeldet haben.",
	'newsletter:log:users:no_recipients' => "Bei der Abwicklung des Newsletters gab es keine registrierten Benutzer. Wenn Sie einige Benutzer ausgewählt hätten, könnten sie sich vom Newsletter abgemeldet haben.",
	
	// group
	'newsletter:group:tool_option' => "Gruppen-Newsletters aktivieren",
	'newsletter:group:error:not_enabled' => "Für diese Gruppe sind keine Newsletters freigegeben",
	'newsletter:group:title' => "%s's Newsletter",
	
	// widget
	'newsletter:widget:subscribe:description' => "Platziere ein Widget zur Newsletter-Anmeldung",
	
	// actions
	// edit
	'newsletter:action:edit:error:title' => "Bitte geben Sie einen Titel für den Newsletter an",
	'newsletter:action:edit:error:from' => "An die gelieferte E-Mail-Adresse darf keine Newsletters versendet werden",
	'newsletter:action:edit:error:save' => "Beim Speichern des Newsletters ist ein unbekannter Fehler aufgetreten. Bitte versuchen Sie es erneut",
	'newsletter:action:edit:success' => "Der Newsletter wurde gespeichert",
	
	// delete
	'newsletter:action:delete:error:delete' => "Beim Löschen des Newsletters ist ein unbekannter Fehler aufgetreten. Bitte versuchen Sie es erneut",
	'newsletter:action:delete:success' => "Der Newsletter wurde gelöscht",
	
	// schedule
	'newsletter:action:schedule:success' => "Der Zeitplan wurde gespeichert",
	
	// recipients
	'newsletter:action:recipients:success' => "Die Empfänger wurden gespeichert",
	
	// content
	'newsletter:action:content:success' => "Der Inhalt wurde gespeichert",
	
	// template
	'newsletter:action:template:success' => "Die Vorlage wurde gespeichert",

	// template to custom
	'newsletter:action:template_to_custom:success' => "Vorlage wurde kopiert zu Benutzerdefiniert",
	
	// subscribe
	'newsletter:action:subscribe:error:subscribe' => "Beim Abonnieren ist ein Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:subscribe:error:unsubscribe' => "Beim Abmelden ist ein Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:subscribe:success' => "Du hast den Newsletter erfolgreich abonniert",
	'newsletter:action:subscribe:success:unsubscribe' => "Du hast Dich erfolgreich vom Newsletter abgemeldet",
	
	// subscriptions
	'newsletter:action:subscriptions:error' => "Beim Speichern der Abonnementeinstellungen ist ein unbekannter Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:subscriptions:success' => "Deine Abonnementeinstellungen wurden gespeichert",
	
	// send
	'newsletter:action:send:success' => "Der Newsletter wird gesendet",
	
	// duplicate
	'newsletter:action:duplicate:error' => "Bei der Duplizierung des Newsletters ist ein unbekannter Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:duplicate:success' => "Der Newsletter wurde dupliziert",
	
	// template - edit
	'newsletter:action:template:edit:error' => "Beim Speichern der Vorlage ist ein unbekannter Fehler aufgetreten",
	'newsletter:action:template:edit:success' => "Die Vorlage wurde gespeichert",
	
	// template - delete
	'newsletter:action:template:delete:error:delete' => "Beim Löschen der Newsletter-Vorlage ist ein unbekannter Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:template:delete:success' => "Die Newsletter-Vorlage wurde gelöscht",
	
	// preview mail
	'newsletter:action:preview_mail:success' => "E-Mail gesendet, überprüfe Deinen Posteingang",
	
	// unsubscribe
	'newsletter:action:unsubscribe:error:recipient' => "Ungültiger Empfänger zum Abmelden",
	'newsletter:action:unsubscribe:error:all' => "Bei der Abmeldung von allen Newslettern ist ein Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:unsubscribe:error:entity' => "Bei der Abmeldung von Deinem %s Newsletter ist ein Fehler aufgetreten. Bitte versuche es erneut",
	'newsletter:action:unsubscribe:success:all' => "Du wurdest von allen Newslettern abgemeldet",
	'newsletter:action:unsubscribe:success:entity' => "Du wurdest vom %s Newsletter abgemeldet",
);
