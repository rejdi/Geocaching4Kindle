<?xml version="1.0" encoding='utf8'?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" version="1.0" encoding="utf8" indent="yes"/>

<xsl:param name="withImages"/>
<xsl:param name="map"/>
<xsl:param name="tocName"/>
<xsl:param name="tocDifficulty"/>
<xsl:param name="tocTerrain"/>
<xsl:param name="meta"/>
<xsl:param name="shortDesc"/>
<xsl:param name="longDesc"/>
<xsl:param name="additionalWaypoints"/>
<xsl:param name="hints"/>
<xsl:param name="solveHint"/>
<xsl:param name="attributes"/>
<xsl:param name="logs"/>

<xsl:template match="/" mode="includes">
	<xsl:if test="$withImages">
		<link rel="stylesheet" href="../../xsl/images.css" type="text/css"/>
	</xsl:if>
</xsl:template>

<xsl:template match="code">
	<xsl:variable name="text" select="normalize-space(.)"/>
	<xsl:attribute name="id"><xsl:value-of select="$text"/></xsl:attribute>
	<xsl:value-of select="$text"/>
</xsl:template>

<xsl:template match="location">
	<xsl:value-of select="normalize-space(.)"/>
</xsl:template>

<xsl:template match="locationUTM">
	<xsl:value-of select="normalize-space(.)"/>
</xsl:template>

<xsl:template match="name">
	<xsl:value-of select="normalize-space(.)"/>
</xsl:template>

<xsl:template match="type">
	<xsl:if test="$withImages">
		<img src="{normalize-space(img)}"/>
	</xsl:if>
	<xsl:if test="not($withImages)">
		<xsl:value-of select="normalize-space(text)"/>
	</xsl:if>
</xsl:template>

<xsl:template match="link">
	<a href="{normalize-space(.)}"><xsl:value-of select="normalize-space(.)"/></a>
</xsl:template>

<xsl:template match="author">
	<xsl:value-of select="normalize-space(.)"/>
</xsl:template>

<xsl:template name="numberToImage">
<xsl:param name="number"/>
<xsl:choose>
	<xsl:when test="$number = '1'">stars1.gif</xsl:when>
	<xsl:when test="$number = '1.5'">stars1_5.gif</xsl:when>
	<xsl:when test="$number = '2'">stars2.gif</xsl:when>
	<xsl:when test="$number = '2.5'">stars2_5.gif</xsl:when>
	<xsl:when test="$number = '3'">stars3.gif</xsl:when>
	<xsl:when test="$number = '3.5'">stars3_5.gif</xsl:when>
	<xsl:when test="$number = '4'">stars4.gif</xsl:when>
	<xsl:when test="$number = '4.5'">stars4_5.gif</xsl:when>
	<xsl:when test="$number = '5'">stars5.gif</xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template match="difficulty">
	<xsl:if test="not($withImages)">
		<xsl:value-of select="."/>
	</xsl:if>
	<xsl:if test="$withImages">
		<img>
		<xsl:attribute name="src"><xsl:call-template name="numberToImage"><xsl:with-param name="number" select="normalize-space(text())"/></xsl:call-template></xsl:attribute>
		</img>
	</xsl:if>
</xsl:template>

<xsl:template match="terrain">
	<xsl:if test="not($withImages)">
		<xsl:value-of select="."/>
	</xsl:if>
	<xsl:if test="$withImages">
		<img>
		<xsl:attribute name="src"><xsl:call-template name="numberToImage"><xsl:with-param name="number" select="normalize-space(text())"/></xsl:call-template></xsl:attribute>
		</img>
		</xsl:if>
</xsl:template>

<xsl:template match="size">
	<xsl:if test="not($withImages)">
		<xsl:value-of select="text"/>
	</xsl:if>
	<xsl:if test="$withImages">
		<img src="{img}"/>
	</xsl:if>
</xsl:template>

<xsl:template match="hintEncrypted">
	<xsl:if test="$hints and not($solveHint)">
		<h3>Hint</h3>
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="hintDecrypted">
	<xsl:if test="$hints and $solveHint">
		<h3>Hint</h3>
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="hintKey">
	<xsl:if test="$hints and not($solveHint)">
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="short-description">
	<xsl:if test="$shortDesc">
		<xsl:copy-of select="*"/>
	</xsl:if>
</xsl:template>

<xsl:template match="long-description">
	<xsl:if test="$longDesc">
		<xsl:copy-of select="*"/>
	</xsl:if>
</xsl:template>

<xsl:template match="log">
	<xsl:if test="position() &lt;= $logs">
		<xsl:if test="position() = 1">
			<h3>Logs</h3>
		</xsl:if>
	
		<h3 class="log_{position() mod 2}"> 
		<span class="type">[<xsl:value-of select="type"/>]</span><span class="time">[<xsl:value-of select="time"/>]</span><span class="user">[<xsl:value-of select="user"/>]</span>
		</h3>
		<dl class="comment log_{position() mod 2}">
			<xsl:copy-of select="comment/*"/>
		</dl>
	</xsl:if>
</xsl:template>

<xsl:template match="meta">
	<xsl:if test="$meta">
		<h3>Meta</h3>
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="attributes">
	<xsl:if test="$attributes">
		<h3>Attributes</h3>
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="map">
	<xsl:if test="$map and $withImages">
		<h3>Map</h3>
		<img src="map_{normalize-space(../code)}.png"/>
	</xsl:if>
</xsl:template>

<xsl:template match="waypoint">
	<xsl:if test="$additionalWaypoints">
		<div class="waypoint">
		<h4><xsl:value-of select="name"/></h4>
		<h5>(<xsl:value-of select="prefix"/>) <xsl:value-of select="lookup"/></h5>
		<span class="position"><xsl:value-of select="position"/></span>
		<p class="note"><xsl:value-of select="note"/></p>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template match="caches" mode="tocName">
	<xsl:if test="$tocName">
	<table class="toc" cellpadding="0" cellspacing="0">
	<tr>
	<td>Name</td><td>Difficulty</td><td>Terrain</td><td>Size</td>
	</tr>
	<xsl:for-each select="cache">
	<xsl:sort select="name" data-type="text"/>
	<tr>
		<td class="sorted_column">
			<a href="#{normalize-space(code)}">
			<xsl:if test="$withImages">
			<xsl:apply-templates select="type"/>
			</xsl:if>
			(<xsl:value-of select="normalize-space(code)"/>)<xsl:value-of select="normalize-space(name)"/></a>
		</td>
		<td>
			<xsl:value-of select="difficulty"/>
		</td>
		<td>
			<xsl:value-of select="terrain"/>
		</td>
		<td>
			<xsl:value-of select="size"/>
		</td>
	</tr>
	</xsl:for-each>
	</table>
	</xsl:if>
</xsl:template>

<xsl:template match="caches" mode="tocDifficulty">
	<xsl:if test="$tocDifficulty">
	<table class="toc" cellpadding="0" cellspacing="0">
	<tr>
	<td>Name</td><td>Difficulty</td><td>Terrain</td><td>Size</td>
	</tr>
	<xsl:for-each select="cache">
	<xsl:sort select="difficulty" data-type="number"/>
	<tr>
		<td>
			<a href="#{normalize-space(code)}">
			<xsl:if test="$withImages">
			<xsl:apply-templates select="type"/>
			</xsl:if>
			(<xsl:value-of select="normalize-space(code)"/>)<xsl:value-of select="normalize-space(name)"/></a>
		</td>
		<td class="sorted_column">
			<xsl:value-of select="difficulty"/>
		</td>
		<td>
			<xsl:value-of select="terrain"/>
		</td>
		<td>
			<xsl:value-of select="size"/>
		</td>
	</tr>
	</xsl:for-each>
	</table>
	</xsl:if>
</xsl:template>

<xsl:template match="caches" mode="tocTerrain">
	<xsl:if test="$tocTerrain">
	<table class="toc" cellpadding="0" cellspacing="0">
	<tr>
	<td>Name</td><td>Difficulty</td><td>Terrain</td><td>Size</td>
	</tr>
	<xsl:for-each select="cache">
	<xsl:sort select="terrain" data-type="number"/>
	<tr>
		<td>
			<a href="#{normalize-space(code)}">
			<xsl:if test="$withImages">
			<xsl:apply-templates select="type"/>
			</xsl:if>
			(<xsl:value-of select="normalize-space(code)"/>)<xsl:value-of select="normalize-space(name)"/></a>
		</td>
		<td>
			<xsl:value-of select="difficulty"/>
		</td>
		<td class="sorted_column">
			<xsl:value-of select="terrain"/>
		</td>
		<td>
			<xsl:value-of select="size"/>
		</td>
	</tr>
	</xsl:for-each>
	</table>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
