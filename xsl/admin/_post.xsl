<?xml version="1.0" ?>
<!-- Merge post dat -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="post">
		<xsl:param name="att"></xsl:param>
		
		<xsl:choose>
			<xsl:when test="//post/@*[name() = $att]">
				<xsl:value-of select="//post/@*[name() = $att]" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@*[name() = $att]" />
			</xsl:otherwise>
		</xsl:choose>
		
	</xsl:template>

</xsl:stylesheet>
