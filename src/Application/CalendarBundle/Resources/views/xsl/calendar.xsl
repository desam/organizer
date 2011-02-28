<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="text" encoding="utf-8" indent="yes" /> 

    <xsl:template match="/">
    [
        <xsl:apply-templates select="//event" />
    ]
    </xsl:template>

    <xsl:template match="event">
        <xsl:if test="position() > 1">,</xsl:if>
        {
          "id": <xsl:value-of select="@id" />
          , "title": "<xsl:value-of select="title" />"
          , "from": "<xsl:value-of select="from" />"
          , "to": "<xsl:value-of select="to" />"
          , "location": "<xsl:value-of select="location" />"
        }
    </xsl:template>
</xsl:stylesheet>

