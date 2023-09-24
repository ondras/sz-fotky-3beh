<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_top.xsl" />
	<xsl:import href="_head.xsl" />
	<xsl:import href="_item.xsl" />

	<xsl:output
		method="html"
		indent="yes"
		omit-xml-declaration="yes"
		doctype-system="about:legacy-compat"
	/>

	<xsl:template match="//admin/chapter">
	<html>
		<xsl:call-template name="head" />
		<body>

			<xsl:call-template name="top">
				<xsl:with-param name="back-text">Zpět do alba</xsl:with-param>
				<xsl:with-param name="back-href">
					<xsl:text>./?action=album&amp;id=</xsl:text>
					<xsl:value-of select="@id_album" />
				</xsl:with-param>
			</xsl:call-template>

			<form method="post" action="./?action=chapter-finish" enctype="multipart/form-data">

				<xsl:element name="input">
					<xsl:attribute name="type">hidden</xsl:attribute>
					<xsl:attribute name="name">id</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="@id" /></xsl:attribute>
				</xsl:element>

				<xsl:element name="input">
					<xsl:attribute name="type">hidden</xsl:attribute>
					<xsl:attribute name="name">id_album</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="@id_album" /></xsl:attribute>
				</xsl:element>

				<table>
					<tbody>
						<xsl:call-template name="item" />

						<tr>
							<td></td>
							<td><input type="submit" value="Uložit" /></td>
						</tr>

					</tbody>
				</table>
			</form>
		</body>
	</html>
	</xsl:template>

</xsl:stylesheet>
