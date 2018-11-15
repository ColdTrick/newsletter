<?php

return array(
	// general
	'item:object:newsletter' => "Lettre d'information",
	'item:object:newsletter_template' => "Gabarit de lettre d'information",
	'item:object:newsletter_subscription' => "Abonnement à la lettre d'information",
	'newsletter:add' => "Nouvelle lettre d'information",
	'newsletter:subscribe' => "S'abonner",
	'newsletter:unsubscribe' => "Se désabonner",
	'newsletter:duplicate_of' => "Duplicata de",
	
	'newsletter:status:concept' => "Conception",
	'newsletter:status:sending' => "Envoi en cours",
	'newsletter:status:scheduled' => "Programmé",
	'newsletter:status:sent' => "Envoyé",
	
	'newsletter:cli:error:secret' => "Secret invalide pour l'utilisation de l'interface en ligne de commande (CLI)",
	
	// CSV upload
	'newsletter:csv:no_email' => "Aucune colonne d'email n'a pu être trouvée dans les 2 premières colonnes du fichier CSV",
	'newsletter:csv:added' => "%s adresses email ajoutées à partir du fichier CSV",
	
	// menu's
	'newsletter:menu:site' => "Lettres d'informations",
	
	'newsletter:menu:page:subscriptions' => "Mes abonnements",
	'newsletter:menu:page:settings' => "Abonnements aux lettres d'information",
	'newsletter:menu:page:received' => "Mes lettres d'information reçues",
	
	'newsletter:menu:entity:log' => "Visualiser le journal",
	'newsletter:menu:entity:duplicate' => "Dupliquer",
	
	'newsletter:menu:owner_block:group' => "Lettre d'information de groupe",
	
	'newsletter:menu:filter:sent' => "Envoyé",
	'newsletter:menu:filter:concept' => "Conception",
	'newsletter:menu:filter:scheduled' => "Programmé",
	'newsletter:menu:filter:sending' => "Envoi en cours",
	
	// steps menu
	'newsletter:menu:steps:entity' => "Informations générales",
	'newsletter:menu:steps:template' => "Gabarit de l'email",
	'newsletter:menu:steps:content' => "Contenu de la Lettre",
	'newsletter:menu:steps:recipients' => "Destinataires",
	'newsletter:menu:steps:schedule' => "Programmation de l'envoi",
		
	// long text menu
	'newsletter:menu:longtext:embed_content' => "Intégrer une publication",
	
	'newsletter:breadcrumb:site' => "Lettres d'information",
	'newsletter:breadcrumb:log' => "Journal d'envoi",
	'newsletter:breadcrumb:received' => "Reçues",
	
	// pages
	'newsletter:site:title' => "Toutes les lettres d'information du site",
	'newsletter:add:title' => "Créer une lettre d'information",
	'newsletter:edit:title' => "Modifier la lettre d'information : %s",
	'newsletter:schedule:title' => "%s : Programmation",
	'newsletter:received:title' => "Lettres d'information reçues par %s",
	'newsletter:received:title:mine' => "Mes lettres d'information reçues",
	
	// embed
	'newsletter:embed:show_all' => "Afficher tout le contenu, pas seulement le contenu du groupe",
	'newsletter:embed:format:description:title' => "Afficher la description",
	'newsletter:embed:format:description:option:full' => "Complet",
	'newsletter:embed:format:description:option:excerpt' => "Extrait",
	'newsletter:embed:format:description:option:no' => "Non",

	'newsletter:embed:format:icon:title' => "Afficher l'icône",
	'newsletter:embed:format:icon:option:left' => "Aligné à gauche",
	'newsletter:embed:format:icon:option:right' => "Aligné à droite",
	'newsletter:embed:format:icon:option:none' => "Ne pas afficher l'icône",
	
	'newsletter:embed:format:add_to_newsletter' => "Ajouter à la newsletter",
	'newsletter:embed:format:preview:title' => "Prévisualiser",
	
	'newsletter:embed:read_more' => "Lire la suite",
	
	// edit
	'newsletter:edit:subject' => "Sujet d'email personnalisé (optionnel)",
	'newsletter:edit:from' => "Adresse email d'expédition personnalisée (optionnel)",
	'newsletter:edit:from:description' => "Par défaut la lettre d'information sera envoyée depuis %s. Vous pouvez définir une adresse d'expédition personnalisée ici. Vous recevrez les notifications ne non-delivérabilité sur cette adresse email.",
	'newsletter:edit:description:description' => "Cette description est utilisée dans la liste des lettres d'information et ne fait pas partie par défaut du contenu de la lettre d'information.",
	
	// placeholders
	'newsletter:placeholders:info' => "Vous pouvez utiliser les éléments suivants dans votre texte. Ils seront remplacés par des textes du système, ou par des informations sur la lettre d'information. Si vous les survolez, vous aurez plus de détails sur chacun d'entre eux.",
	'newsletter:placeholders:content' => "Content sera remplacé par le contenu défini dans l'étape contenu de la lettre d'information (ne pas utiliser sur la page de contenu)",
	'newsletter:placeholders:unsub' => "Unsub sera remplacé par le texte de désabonnement par défaut contenant un lien de désabonnement",
	'newsletter:placeholders:unsublink' => "Unsublink sera remplacé par un lien de désinscription",
	'newsletter:placeholders:online' => "Online sera remplacé par le texte par défaut qui indique où visualiser la lettre d'information en ligne",
	'newsletter:placeholders:title' => "Title sera remplacé par le titre de votre lettre d'information",
	'newsletter:placeholders:description' => "Description sera remplacé par la description de la description de la lettre d'information",
	'newsletter:placeholders:subject' => "Subject sera remplacé par le sujet de la lettre d'information",
	'newsletter:placeholders:newsletter_url' => "Newsletter_url sera remplacé par l'URL de la lettre d'information",
	'newsletter:placeholders:site_name' => "Site_name sera remplacé par le nom du site",
	'newsletter:placeholders:site_description' => "Site_description sera remplacé par la description du site",
	'newsletter:placeholders:site_url' => "Site_url sera remplacé par l'URL du site",
	'newsletter:placeholders:container_name' => "Container_name sera remplacé par le nom du conteneur (peut être le groupe ou le site)",
	'newsletter:placeholders:container_url' => "Container_url sera rempalcé par l'URL du conteneur ()' will be replaced by the url of the container (peut être le groupe ou le site)",
		
	// content
	'newsletter:edit:content:description' => "Ajoutez ici le contenu de votre lettre d'information. Ajoutez du texte libre, ou importez un article de blog existant dans votre lettre d'information.",
				
	// template
	'newsletter:edit:template:description' => "Vous pouvez choisir ici la mise en page de la lettre d'information en changeant le code HTML et la feuille de style via les CSS. N'oubliez pas de prévisualiser votre lettre d'information pour vérifier qu'elle a bien l'apparence souhaitée.",
	'newsletter:edit:template:copy_to_custom' => "Copier vers les gabarits personnalisés",
	'newsletter:edit:template:copy_to_custom:confirm' => "Cette action va remplacer le gabarit personnalisé actuel. Etes-vous sûr de vouloir copier ce gabarit vers le gabarit personnalisé pour cette lettre d'information ?",
	'newsletter:edit:template:select' => "Sélectionnez un gabarit",
	'newsletter:edit:template:select:default' => "Gabarit par défaut",
	'newsletter:edit:template:select:default2' => "Gabarit par défaut (avec barre latérale)",
	'newsletter:edit:template:select:custom' => "Gabarit personnalisé",
	'newsletter:edit:template:html' => "HTML",
	'newsletter:edit:template:css' => "CSS",
	'newsletter:edit:template:name' => "Nom du nouveau gabarit (nécessaire uniquement pour sauvegarder le gabarit)",
	'newsletter:edit:template:save_as' => "Sauvegarder comme gabarit",
	'newsletter:edit:template:error:save_as' => "Veuillez vérifier les champs marqués comme obligatoires",
		
	// default template body
	'newsletter:body:unsub' => "Cette lettre d'information est envoyée à partir de <a href='{site_url}' rel='nofollow'>{site_name}</a>. Cliquez <a href='{unsublink}'>ici pour vous désabonner</a> de cette lettre d'information.",
	'newsletter:body:online' => "Si vous n'arrivez pas à lire cette lettre d'information, veuillez la <a href='{newsletter_url}'>consulter en ligne</a>",
		

	// schedule
	'newsletter:schedule:description' => "Vous pouvez configurer ici quand la lettre d'information sera envoyée aux destinataires sélectionnés.",
	'newsletter:schedule:date' => "Date programmée",
	'newsletter:schedule:time' => "Heure programmée",
	'newsletter:schedule:status_notification' => "Adresse email pour la notification d'envoi (optionnel)",
	'newsletter:schedule:status_notification:description' => "Lorsque la lettre d'information sera envoyée, une notification sera envoyée à cette adresse email.",
	'newsletter:schedule:status_notification:me' => "M'envoyer une notification de statut",
	'newsletter:schedule:status_notification:toggle' => "ou saisir une adresse email personnalisée",
	'newsletter:schedule:show_in_archive' => "Montrer la lettre d'information dans l'archive",
	'newsletter:schedule:send' => "Enregistrer et envoyer maintenant",
	'newsletter:schedule:save' => "Enregistrer et programmer l'envoi ultérieur",
	'newsletter:schedule:no_recipients' => "Vous n'avez sélectionné aucun destinataire, confirmez-vous vouloir poursuivre cette action ?",

	// recipients
	'newsletter:recipients:title' => "Sélectionner les destinataires",
	'newsletter:recipients:tooltip' => "Cocher tous les membres ? Dans ce cas vous envoyez la lettre d'information à tous les membres du groupe (sauf ceux qui ont indiqué qu'ils ne souhaitent pas recevoir la lettre d'information). Par Tous les abonnés on entend tous les abonnés à la lettre d'information qui ne sont pas nécessairement membres de votre groupe mais qui ont un abonnement à la lettre d'information. Si le nombre derrière les abonnés est un (0), alors cette lettre d'information n'a aucun abonné.",
	'newsletter:recipients:description' => "Vous pouvez choisir ci-dessous les destinataires de cette lettre d'information.",
	'newsletter:recipients:csv' => "Envoyer un fichier CSV avec les adresses email de destinataires",
	'newsletter:recipients:csv:description' => "Vous pouvez envoyer un fichier contenant les adresses email des destinataires. Le délimiteur de texte doit être \" (guillemet double), et le délimiteur de colonnes doit être un ; (point-virgule). Le système détectera automatiquement la colonne des adresses email en recherchant parmi les 2 premières colonnes.",
	'newsletter:recipients:recipient' => "Rechercher un destinataire",
	'newsletter:recipients:recipient:description' => "Vous pouvez faire une recherche par nom, par email ou par nom d'utilisateur. Veuillez sélectionner une entrée de la liste pour l'ajouter aux destinataires.",
	'newsletter:recipients' => "Destinataires",
	'newsletter:recipients:subscribers' => "A tous les abonnés",
	'newsletter:recipients:members' => "A tous les membres",
	'newsletter:recipients:members:site' => "membres du site",
	'newsletter:recipients:members:group' => "membres du groupe",
	'newsletter:recipients:email' => "Adresse email",
	
	// plugin settings
	'newsletter:settings:allow_groups' => "Autoriser les responsables de groupes à envoyer des lettres d'information",
	'newsletter:settings:allow_groups:description' => "Les responsables de groupes peuvent créer des lettres d'information pour les membres de leur groupe.",
	'newsletter:settings:include_existing_users' => "Inclure les membres sans paramètre d'abonnement",
	'newsletter:settings:include_existing_users:description' => "Lorsque ce réglage est positionné sur 'Non', tous les membres sans paramètre d'abonnement ne recevront plus de lettre d'information.",
	'newsletter:settings:custom_from' => "Permettre aux lettres d'information d'être envoyées depuis une adresse email personnalisée",
	'newsletter:settings:custom_from:description' => "Quand ce réglage est sur 'oui', les membres sont autorisés à saisir une adresse email personnalisée qui sera utilisée comme adresse d'expédition de la lettre d'information. Veuillez garder à l'esprit que ceci peut mener à des abus et peuvent augmenter les risques que les lettres d'information soient classées comme spam plus fréquemment.",
	'newsletter:settings:custom_from:domains' => "Limiter les adresses email personnalisées aux domaines suivants",
	'newsletter:settings:custom_from:domains:description' => "Saisissez une liste de domaines spéarés par des virgules pour limiter les adresses email personnalisées. Par exemple : example.com, ceci autorisera user@example.com mais pas user@example2.com ni user2@sub.example.com",
	
	'newsletter:settings:url_postfix' => "Paramètres d'URL postfix",
	'newsletter:settings:url_postfix:description' => "Vous pouvez configurer un ou plusieurs couple no/valeur pour postfix, qui seront placées dans tous les liens (internes) de la lettre d'information. Vous pouvez configurer un couple nom/valeur par ligne dans le format 'nom=valeur' (par ex.: source=newsletter). Ceci permettra aux systèmes de suivi d'identifier que les visiteurs provenaient de la lettre d'information.",
	'newsletter:settings:url_postfix:setting' => "Paramètres Postfix",
	'newsletter:settings:url_postfix:setting:description' => "Il y a certain placeholders que vous pouvez utiliser dans vos valeurs postfix : %s",
	
	// entity view
	'newsletter:entity:scheduled' => "Programmé",
	'newsletter:entity:sent' => "Envoyé",
	'newsletter:entity:error:code' => "Invalide, ou code manquant pour afficher la lettre d'information en ligne",
	
	// my subscriptions
	'newsletter:subscriptions:description' => "Vous pouvez gérer ci-dessous l'ensemble de vos abonnements aux lettres d'informations.",
	'newsletter:subscriptions:site:title' => "Lettre d'information du site",
	'newsletter:subscriptions:site:description' => "Souhaitez-vous recevoir une lettre d'information du réseau ?",
	'newsletter:subscriptions:groups:title' => "Mes lettres d'information de groupe",
	'newsletter:subscriptions:groups:description' => "Tous les groupes dont vous êtes membre sont listés, aussi vous pouvez aisément modifier l'ensemble de vos abonnements aux lettres d'information.",
	'newsletter:subscriptions:other:title' => "Autres abonnements",
	'newsletter:subscriptions:other:description' => "Si vous souhaitez recevoir la lettre d'information d'un groupe dont vous n'êtes pas membre, elles sont listées ici.",
	
	// sidebar - steps
	'newsletter:sidebar:steps' => "Etapes",

	// unsubscribe
	'newsletter:unsubscribe:error:input' => "Saisie incorrecte, veuillez vérifier le lien reçu par email",
	'newsletter:unsubscribe:error:code' => "Code de désabonnement invalide, veuillez vérifier le lien reçu par email",
	'newsletter:unsubscribe:title' => "Se désabonner de cete lettre d'information",
	'newsletter:unsubscribe:user' => "Bonjour %s,

Veuillez vérifier les deux paramètres ci-dessous et cliquer sur le lien de désabonnement pour terminer le processus.",
	'newsletter:unsubscribe:email' => "Votre adresse email %s sera désabonnée si vous cliquez sur le bouton ci-dessous.",
	'newsletter:unsubscribe:email:empty' => "Saisissez votre adresse email et cliquez sur le bouton de désabonnement ci-dessous pour vous désabonner.",
	'newsletter:unsubscribe:entity' => "Je ne souhaite plus recevoir la lettre \"%s\"",
	'newsletter:unsubscribe:all' => "Je ne souhaite plus recevoir aucune lettre d'information du réseau %s",
	
	// sidebar - subscribe
	'newsletter:sidebar:subscribe:title' => "Abonnements aux lettres d'information",
	'newsletter:subscribe:email:description' => "S'abonner à la lettre d'information %s",
	'newsletter:subscribe:user:description:subscribe' => "S'abonner à la lettre d'information %s",
	'newsletter:subscribe:user:description:unsubscribe' => "Se désabonner de la lettre d'information %s",
	
	// registration
	'newsletter:registration' => "Je souhaite recevoir les lettres d'information du site",
	
	// email content
	'newsletter:subject' => "Lettre d'information de %s : %s",
	'newsletter:plain_message' => "Pour afficher correctement la lettre d'information, votre logiciel de messagerie doit supporter les messages HTML.

Pour afficher la lettre d'information en ligne, veuillez cliquer sur :
%s",
	
	// status notification
	'newsletter:status_notification:subject' => "Notification d'envoi de la lettre d'information",
	'newsletter:status_notification:message' => "LS,

Votre lettre d'information '%s' a été envoyée.

Pour afficher cette lettre d'information, cliquez sur :
%s",
	
	// logging
	'newsletter:log:title' => "Journal d'envoi : %s",
	'newsletter:log:counter:success' => "envoyé",
	'newsletter:log:counter:error' => "erreurs",
	
	'newsletter:log:users:title' => "Membres",
	'newsletter:log:users:header:email' => "Adresse email",
	'newsletter:log:users:header:time' => "Horaire",
	'newsletter:log:users:header:status' => "Statut",
	'newsletter:log:users:header:guid' => "Membre",
	
	'newsletter:log:emails:title' => "Adresses email",
	'newsletter:log:email:header:email' => "Adresses email",
	'newsletter:log:email:header:time' => "Horaire",
	'newsletter:log:email:header:status' => "Statut",
	
	'newsletter:log:general:title' => "Information générale",
	'newsletter:log:general:scheduled' => "Horaire de programmation",
	'newsletter:log:general:starttime' => "Heure de démarrage",
	'newsletter:log:general:endtime' => "Traitement terminé",
	
	'newsletter:log:no_contents' => "Aucun fichier de journal trouvé, êtes-vous sûr que cette lettre d'information a déjà été envoyée ? L'une des causes possibles est qu'aucun destinataire n'a été défini pour cet envoi.",
	'newsletter:log:no_recipients' => "Aucun destinataire pour cette lettre d'information",
	'newsletter:log:emails:no_recipients' => "Il n'y avait aucune adresse email individuelle lors du traitement de la lettre d'information. Si vous aviez sélectionné des adresses email, les personnes ont pu se désinscrire de la lettre d'information.",
	'newsletter:log:users:no_recipients' => "Il n'y avait aucun membre enregistré lors du traitement de la lettre d'information. Si vous aviez sélectionné des membres, ils ont pu se désinscrire de la lettre d'information.",
	
	// group
	'newsletter:group:tool_option' => "Activer les lettres d'information du groupe",
	'newsletter:group:error:not_enabled' => "Les lettres d'information ne sont pas activées pour ce groupe",
	'newsletter:group:title' => "Lettres d'information de %s",
	
		// widget
		'newsletter:widget:subscribe:description' => "Afficher un widget d'inscription rapide à la lettre d'information",
	
	// actions
	// edit
	'newsletter:action:edit:error:title' => "Veuillez donner un titre à la lettre d'information",
	'newsletter:action:edit:error:from' => "L'adresse email indiquée n'est pas autorisée à envoyer de lettre d'information",
	'newsletter:action:edit:error:save' => "Une erreur inconnue s'est produite lors de la sauvegarde de la lettre d'information, veuillez ré-essayer",
	'newsletter:action:edit:success' => "La lettre d'information a été sauvegardée",
	
	// delete
	'newsletter:action:delete:error:delete' => "Une erreur inconnue s'est produite lors de la suppression de la lettre d'information, veuillez ré-essayer",
	'newsletter:action:delete:success' => "La lettre d'information a été supprimée",
	
	// schedule
	'newsletter:action:schedule:success' => "Programmation sauvegardée",
	
	// recipients
	'newsletter:action:recipients:success' => "Destinataires sauvegardés",
	
	// content
	'newsletter:action:content:success' => "Contenu enregistré",
	
	// template
	'newsletter:action:template:success' => "Gabarit sauvegardé",

	// template to custom
	'newsletter:action:template_to_custom:success' => "Gabarit copié vers les gabarits personnalisés",
	
	// subscribe
	'newsletter:action:subscribe:error:subscribe' => "Une erreur s'est produite lors de l'abonnement, veuillez ré-essayer",
	'newsletter:action:subscribe:error:unsubscribe' => "Une erreur s'est produite lors du désabonnement, veuillez ré-essayer",
	'newsletter:action:subscribe:success' => "Vous avez bien été abonné à la lettre d'information",
	'newsletter:action:subscribe:success:unsubscribe' => "Vous avez bien été désabonné de la lettre d'information",
	
	// subscriptions
	'newsletter:action:subscriptions:error' => "Une erreur inconnue s'est produite lors de lors de la sauvegarde de vos paramètres d'abonnement, veuillez ré-essayer",
	'newsletter:action:subscriptions:success' => "Vos paramètres d'abonnement ont bien été sauvegardés",
	
	// send
	'newsletter:action:send:success' => "La lettre d'information est en cours d'envoi",
	
	// duplicate
	'newsletter:action:duplicate:error' => "Une erreur inconnue s'est produite lors de la duplicaiton de la lettre d'information, veuillez ré-essayer",
	'newsletter:action:duplicate:success' => "La lettre d'information a été dupliquée",
	
	// template - edit
	'newsletter:action:template:edit:error' => "Une erreur inconnue s'est produite lors de la sauvegarde du gabarit",
	'newsletter:action:template:edit:success' => "Le gabarit a été sauvegardé",
	
	// template - delete
	'newsletter:action:template:delete:error:delete' => "Une erreur inconnue s'est produite lors de la suppression du gabarit de lettre d'informaiton, veuillez ré-essayer",
	'newsletter:action:template:delete:success' => "La lettre d'information a été supprimée",
	
	// preview mail
	'newsletter:action:preview_mail:success' => "Mail envoyé, veuillez vérifier votre boîte de réception",
	
	// unsubscribe
	'newsletter:action:unsubscribe:error:recipient' => "Destinataire à désabonner invalide",
	'newsletter:action:unsubscribe:error:all' => "Une erreur s'est produite lors de votre désabonnement de toutes les lettres d'information, veuillez ré-essayer",
	'newsletter:action:unsubscribe:error:entity' => "Une erreur s'est produite lors de votre désabonnement de la lettre d'information %s, veuillez ré-essayer",
	'newsletter:action:unsubscribe:success:all' => "Vous avez été désabonné de toutes les lettres d'information",
	'newsletter:action:unsubscribe:success:entity' => "Vous avez été désabonné de la lettre d'information %s",
	
);

