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
		<link rel="stylesheet" href="images.css" type="text/css"/>
	</xsl:if>
</xsl:template>

<xsl:template match="code">
	<xsl:attribute name="id" select="."/>
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="location">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="locationUTM">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="name">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="type">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="link">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="author">
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="difficulty">
	<xsl:if test="not($withImages)">
		difficulty:
	</xsl:if>
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="terrain">
	<xsl:if test="not($withImages)">
		terrain:
	</xsl:if>
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="size">
	<xsl:if test="not($withImages)">
		size:
	</xsl:if>
	<xsl:value-of select="."/>
</xsl:template>

<xsl:template match="hintEncrypted">
	<xsl:if test="$hints and not($solveHint)">
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="hintDecrypted">
	<xsl:if test="$hints and $solveHint">
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
		<h3 class="user"><xsl:value-of select="user"/></h3>
		<h4 class="type"><xsl:value-of select="type"/></h4>
		<span class="time"><xsl:value-of select="time"/></span>
		<p class="comment"><xsl:value-of select="comment"/></p>
	</xsl:if>
</xsl:template>

<xsl:template match="meta">
	<xsl:if test="$meta">
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="attributes">
	<xsl:if test="$attributes">
		<xsl:value-of select="."/>
	</xsl:if>
</xsl:template>

<xsl:template match="map">
	<xsl:if test="$map and $withImages">
		<xsl:copy-of select="*"/>
		<img id="map"/>
	</xsl:if>
</xsl:template>

<xsl:template match="waypoint">
	<xsl:if test="$additionalWaypoints">
		<!-- you have to place div id="map" inside result //-->
		<h3><xsl:value-of select="name"/></h3>
		<h4>(<xsl:value-of select="prefix"/>) <xsl:value-of select="lookup"/></h4>
		<span class="position"><xsl:value-of select="name"/></span>
		<p class="note"><xsl:value-of select="note"/></p>
	</xsl:if>
</xsl:template>

<xsl:template match="caches" mode="tocName">
	<xsl:if test="$tocTerrain">
	<table>
	<tr>
	<td>Name</td><td>Difficulty</td><td>Terrain</td><td>Size</td>
	</tr>
	<xsl:for-each select="cache">
	<xsl:sort select="name" data-type="text"/>
	<tr>
		<td>
			<a href="#{code}"><xsl:value-of select="name"/></a>
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
	<xsl:if test="$tocTerrain">
	<table>
	<tr>
	<td>Name</td><td>Difficulty</td><td>Terrain</td><td>Size</td>
	</tr>
	<xsl:for-each select="cache">
	<xsl:sort select="difficulty" data-type="number"/>
	<tr>
		<td>
			<a href="#{code}"><xsl:value-of select="name"/></a>
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

<xsl:template match="caches" mode="tocTerrain">
	<xsl:if test="$tocTerrain">
	<table>
	<tr>
	<td>Name</td><td>Difficulty</td><td>Terrain</td><td>Size</td>
	</tr>
	<xsl:for-each select="cache">
	<xsl:sort select="terrain" data-type="number"/>
	<tr>
		<td>
			<a href="#{code}"><xsl:value-of select="name"/></a>
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

</xsl:stylesheet>
