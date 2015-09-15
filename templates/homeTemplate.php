<!DOCTYPE html>
<html>
	<head>
		<title>Placing Bad</title>
		<link rel="stylesheet" type="text/css" href="assets/css/main.css" />
	</head>

	<body>
        <header>
            <div class="content">
    			<h1>Placing Bad</h1>
                <p>This is a placeholder generator for developers to add to their sites, to help with testing. But what developer doesn't like Breaking Bad.</p>
    			<p class="quote">&ldquo;I am the one who...adds placeholders to my site&rdquo;</p>
            </div>
        </header>

        <main>
            <div class="content">
    			<div class="left half">
                    <h2>How to use:</h2>
        			<p><strong>If you know the width and height:</strong></p>
        			<pre>
        				<code>{{ currenturl }}200/200
{{ currenturl }}width/height</code>
        			</pre>

        			<p><strong>If you want a square, just specify the width:</strong></p>
        			<pre>
        				<code>{{ currenturl }}200
{{ currenturl }}width</code>
        			</pre>

                    <p><strong>Want a certain character...use any below</strong></p>
                    <pre>
                        <code>{% for person in people %}
{{ currenturl }}width/height/<strong>{{ person }}</strong>
{% endfor %}</code>
                    </pre>
                </div>

                <div class="right half">
                	<img src="{{ currenturl }}500/380" />
                    <img src="{{ currenturl }}800" />
                    <img src="{{ currenturl }}500/200/gus" />
                </div>

            </div>
		</main>

	</body>
</html>
