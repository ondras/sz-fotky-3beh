<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_head.xsl" />
	<xsl:import href="_top.xsl" />

	<xsl:output
		method="html"
		indent="yes"
		omit-xml-declaration="yes"
		doctype-system="about:legacy-compat"
	/>

	<xsl:template match="//admin">
	<html>
		<xsl:call-template name="head" />
		<body>
			<xsl:call-template name="top">
				<xsl:with-param name="menu">0</xsl:with-param>
			</xsl:call-template>

			<form action="./?action=login-finish" method="post">
				<p><input type="password" name="pass" autofocus="autofocus" /></p>
				<p><input type="submit" value="Přihlásit" /></p>
			</form>

		</body>
	</html>
	</xsl:template>

</xsl:stylesheet>
