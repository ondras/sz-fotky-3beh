<?xml version="1.0" ?>
<!-- Zahlavi: menu + chyba -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_title.xsl" />
	<xsl:import href="_link.xsl" />
	
	<xsl:template name="top">
		<xsl:param name="back-text" select="''" />
		<xsl:param name="back-href" select="''" />
		<xsl:param name="menu" select="1" />
		
		<header>
			<xsl:if test="$back-href != ''">
				<xsl:call-template name="link">
					<xsl:with-param name="href"><xsl:value-of select="$back-href" /></xsl:with-param>
					<xsl:with-param name="text"><xsl:value-of select="$back-text" /></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>
			</xsl:if>
			
			<xsl:if test="$menu = 1">
				<xsl:call-template name="link">
					<xsl:with-param name="href">./</xsl:with-param>
					<xsl:with-param name="text">Seznam alb</xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>
				<xsl:call-template name="link">
					<xsl:with-param name="href">./?action=config</xsl:with-param>
					<xsl:with-param name="text">Nastavení</xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>
				<xsl:call-template name="link">
					<xsl:with-param name="href">./?action=logout</xsl:with-param>
					<xsl:with-param name="text">Odhlásit</xsl:with-param>
				</xsl:call-template>
			</xsl:if>
		</header>
		
		<h1><xsl:call-template name="title" /></h1>
		<xsl:if test="//admin/@error">
			<p class="error"><xsl:value-of select="//admin/@error" /></p>
		</xsl:if>
		
		
	</xsl:template>

</xsl:stylesheet>
