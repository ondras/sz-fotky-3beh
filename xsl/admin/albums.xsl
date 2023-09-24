<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_top.xsl" />
	<xsl:import href="_head.xsl" />

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
			<xsl:call-template name="top" />

			<xsl:for-each select="years/year">
				[<xsl:element name="a">
					<xsl:if test=". != //admin/albums/@year">
						<xsl:attribute name="href">./?year=<xsl:value-of select="." /></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="." />
				</xsl:element>]
			</xsl:for-each>

			<p>
				<a href="./?action=album&amp;id=0"><img src="/img/admin/add.png" title="Nové album" /></a>
				<xsl:text> </xsl:text>
				<a href="./?action=album&amp;id=0">Nové album</a>
			</p>

			<table>
				<tbody>
					<xsl:for-each select="albums/album">
						<xsl:element name="tr">
							<xsl:if test="@visible = '0'">
								<xsl:attribute name="class">inactive</xsl:attribute>
							</xsl:if>
							<td>
								<xsl:if test="@public = '1'">
									<img src="/img/admin/important.png" title="Veřejné!" />
									<xsl:text> </xsl:text>
								</xsl:if>
								<xsl:if test="@photo != ''">
									<img src="/img/admin/camera.png" title="Vybraná hlavní fotka" />
									<xsl:text> </xsl:text>
								</xsl:if>
								<xsl:element name="a">
									<xsl:attribute name="href">
										<xsl:text>./?action=album&amp;id=</xsl:text>
										<xsl:value-of select="@id" />
									</xsl:attribute>
									<xsl:value-of select="@name" />
								</xsl:element>
								<xsl:text> </xsl:text>
								<a target="_blank" href="/{@year}/{@shortcut}">
									<img src="/img/admin/view.png" title="Prohlédnout" />
								</a>
								<span><xsl:value-of select="@shortcut" /></span>
							</td>
							<td>
								<xsl:value-of select="@start" />
								<xsl:if test="@end != ''">
									<xsl:text> – </xsl:text>
									<xsl:value-of select="@end" />
								</xsl:if>
							</td>
							<td>
								<xsl:element name="a">
									<xsl:attribute name="href">
										<xsl:text>./?action=album-delete&amp;id=</xsl:text>
										<xsl:value-of select="@id" />
									</xsl:attribute>
									<xsl:attribute name="class">confirm</xsl:attribute>
									<img src="/img/admin/delete.png" title="Smazat album" />
								</xsl:element>
							</td>

						</xsl:element>
					</xsl:for-each>
				</tbody>
			</table>
		</body>
	</html>
	</xsl:template>
</xsl:stylesheet>
