<?xml version="1.0" ?>
<!-- Nadpis/titulek -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="title">
		<xsl:value-of select="//admin/@title" />
	</xsl:template>
</xsl:stylesheet>
