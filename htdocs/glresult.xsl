<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" omit-xml-declaration="yes" indent="yes"/>
    <xsl:template match="node()|@*">
    	<xsl:copy>
       		<xsl:apply-templates select="node()|@*"/>
    	</xsl:copy>
    </xsl:template>
</xsl:stylesheet>