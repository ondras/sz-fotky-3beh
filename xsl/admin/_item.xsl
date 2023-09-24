<?xml version="1.0" ?>
<!-- Spolecne radky formulare pro galerii i kapitolu -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_input.xsl" />

	<xsl:template name="item">
		<tr>
			<td>Název:</td>
			<td>
				<xsl:call-template name="input">
					<xsl:with-param name="name">name</xsl:with-param>
				</xsl:call-template>
			</td>
		</tr>

		<tr>
			<td>Adresář:</td>
			<td>
				<xsl:call-template name="input">
					<xsl:with-param name="name">directory</xsl:with-param>
				</xsl:call-template>
			</td>
		</tr>

		<tr>
			<td>Zkratka:</td>
			<td>
				<xsl:call-template name="input">
					<xsl:with-param name="name">shortcut</xsl:with-param>
				</xsl:call-template>
			</td>
		</tr>

		<tr>
			<td>GPX:</td>
			<td>
				<xsl:choose>
					<xsl:when test="@gpx = '1'">
						<label><input type="checkbox" name="gpx-delete" value="1" />&#x2009;smazat</label>
					</xsl:when>
					<xsl:otherwise>
						<input type="file" name="gpx" />
					</xsl:otherwise>
				</xsl:choose>
			</td>
		</tr>

		<tr>
			<td>Veřejné:</td>
			<td>
				<xsl:element name="input">
					<xsl:attribute name="type">checkbox</xsl:attribute>
					<xsl:attribute name="name">public</xsl:attribute>
					<xsl:attribute name="value">1</xsl:attribute>
					<xsl:if test="@public = '1'">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>
				</xsl:element>
			</td>
		</tr>
		<tr>
			<td>Povoleno:</td>
			<td>
				<xsl:element name="input">
					<xsl:attribute name="type">checkbox</xsl:attribute>
					<xsl:attribute name="name">visible</xsl:attribute>
					<xsl:attribute name="value">1</xsl:attribute>
					<xsl:if test="@visible = '1'">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>
				</xsl:element>
			</td>
		</tr>

	</xsl:template>
</xsl:stylesheet>
