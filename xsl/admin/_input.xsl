<?xml version="1.0" ?>
<!-- Input[type=text] -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_post.xsl" />
	
	<xsl:template name="input">
		<xsl:param name="name" />
		<xsl:param name="id"></xsl:param>
		<xsl:param name="class"></xsl:param>
		<xsl:param name="type">text</xsl:param>

		<xsl:variable name="value">
			<xsl:call-template name="post">
				<xsl:with-param name="att" select="$name" />
			</xsl:call-template>
		</xsl:variable>
		
		<xsl:element name="input">
			<xsl:attribute name="type"><xsl:value-of select="$type" /></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
			<xsl:if test="$id != ''">
				<xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="$class != ''">
				<xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
			</xsl:if>
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>
