<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title>Eine Anfrage bei Padento.de</title>
	</head>
	<body>
		<p>Folgendes Labor würde gerne bei Padento mitmachen:</p>
		<p>Name des Labors: {{ $data['laborname'] }}<br/>
		Ansprechpartner: {{ $data['kontaktperson'] }}<br/>
		E-Mail: {{ $data['email'] }}<br/>
		Telefonnummer: {{ $data['tel'] }}
		<p>Bitte schicke dem Labor folgenden Link damit sich das Labor endgültig registrieren kann:</p>
		<p><a href='https://padento.de/neues-labor'>https://padento.de/neues-labor</a></p>
	</body>
</html>