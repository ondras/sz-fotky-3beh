<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_head_simple.xsl" />
	<xsl:import href="../_.xsl" />
	<xsl:param name="language" select="'cz'" />

	<xsl:output
		method="html"
		indent="yes"
		omit-xml-declaration="yes"
		doctype-system="about:legacy-compat"
	/>

	<xsl:template match="//user">
		<html>
			<xsl:variable name="text" >
				<xsl:call-template name="_"><xsl:with-param name="key" select="'password.title'" /></xsl:call-template>
			</xsl:variable>
			<xsl:variable name="submit" >
				<xsl:call-template name="_"><xsl:with-param name="key" select="'password.submit'" /></xsl:call-template>
			</xsl:variable>

			<xsl:call-template name="head"><xsl:with-param name="title" select="$text" /></xsl:call-template>

			<body>
				<h1><xsl:value-of select="$text" />...</h1>
				<p>
					<xsl:call-template name="_"><xsl:with-param name="key" select="'password.description'" /></xsl:call-template>
				</p>
				<form method="post" action="{@url}">
					<input type="password" name="pass" autofocus="autofocus" />
					<input type="submit" value="{$submit}" />
				</form>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
