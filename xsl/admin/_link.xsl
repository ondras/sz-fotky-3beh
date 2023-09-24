<?xml version="1.0" ?>
<!-- Odkaz -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="link">
		<xsl:param name="text" />
		<xsl:param name="href" />

		<span><a href="{$href}"><xsl:value-of select="$text" /></a></span>
	</xsl:template>
</xsl:stylesheet>
