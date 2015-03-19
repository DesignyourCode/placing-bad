<!DOCTYPE html>
<html>
	<head>
		<title>Placing Bad</title>
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>

	<body>
		<main>
			<h1>Placing Bad</h1>
			<p>I am the one who...adds placeholders to my site ;)</p>

			<h2>How to use:</h2>
			<p><strong>If you know the width and height:</strong></p>
			<pre>
				<code>
					{{ currenturl }}200/200
					{{ currenturl }}width/height
				</code>
			</pre>

			<p><strong>If you want a square, just specify the width:</strong></p>
			<pre>
				<code>
					{{ currenturl }}200
					{{ currenturl }}width
				</code>
			</pre>

      <p><strong>Want a certain character...use any below</strong></p>
      <pre>
        <code>
          {% for person in people %}
            {{ currenturl }}{{ person }}/width/height
          {% endfor %}
        </code>
      </pre>

			<img src="{{ currenturl }}500/380" />
		</main>

	</body>
</html>
