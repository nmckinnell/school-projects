# musicPlayer.py
# Author: Nora McKinnell
# Feb 12, 2012

import json
from pprint import pprint

firstPart = \
"""
<html>
    <head>
        <title>CS 349a Music Player</title>
	<link rel=stylesheet type="text/css" href="style.css">
    </head>
    <body>
	<div id="main">
"""

endPart = \
"""
		
    </body>
</html>
"""

# Get file info from json file for page
def getFileInfo(fileName):
    f = open(fileName)
    track = json.load(f)
    f.close()

    info = {}
    info['artistName'] = track['track']['artist']['name']
    info['artistUrl'] = track['track']['artist']['url']
    info['trackName'] = track['track']['name']
    info['trackLength'] = track['track']['duration'] # need to format
    info['trackUrl'] = track['track']['url']
    info['playcount'] ='{:20,d}'.format(int(track['track']['playcount']))
    info['listeners'] ='{:20,d}'.format(int(track['track']['listeners']))
    info['album'] = track['track']['album']['title']
    info['albumUrl'] = track['track']['album']['url']
    info['cover'] = track['track']['album']['image'][3]['#text']
    info['coverIcon'] = track['track']['album']['image'][2]['#text']
    
    # Checks for tags
    info['tags'] = ""
    if track['track']['toptags'] != "\n      ": # Showed up in 'northeast'
        numTags = len(track['track']['toptags']['tag'])
        i = 0
        while i < numTags:
            info['tags'] += str(track['track']['toptags']['tag'][i]['name'])
            if i != numTags-1:
                info['tags'] += ", "
            i += 1
        
    return info

# Get info for similar tracks to be displayed on main page
def getSimilarInfo(fileName):
    f = open(fileName)
    tracks = json.load(f)
    f.close()

    # pprint(tracks)

    trackList = []
    for i in range(0,5):
        trackList.append({'artistName': tracks[i]['artist']['name'], 'coverIcon': tracks[i]['image'][2]['#text']})

    return trackList

def write_entry(info, trackList):
    "Write the HTML code for one item of the list"
    
    entry = \
    """
    <div class="item">

		
        <img class="album" src="%s">

        <ul class="info">
        <li><span class="trackTitle"><a href="%s">%s</a> - <a href="%s">%s</a></span></li>
        <li>%s plays (%s listeners )</li>
        <p>
        <li>From the album: <a href="%s">%s</a></li>
        </p>
        <p>
        <li>Tagged as: %s</li>
        </p>

        </ul>


    </div>
    """ % (info['cover'], info['artistUrl'], info['artistName'],
           info['trackUrl'], info['trackName'], info['playcount'],
           info['listeners'], info['albumUrl'],
           info['album'], info['tags'])

    if trackList != '':
        for i in range(0,5):
            entry += \
            """
            <div align="center" class="group">	
            <img class="similar" src="%s">
            <a href="album-%s.html">%s</a>
            </div>
            """ % (trackList[i]['coverIcon'], i, trackList[i]['artistName'])
            
    else:
            entry += \
            """
            </div>
            <div class="back">
            <a href="album-main.html">Back to main album</a>
            </div>  
            """
    
    return entry

def write_HTML_file(jsonFile, fileName, trackList=''):
    middlePart = ""

    middlePart += write_entry(getFileInfo(jsonFile), trackList)
     
    html = firstPart + middlePart + endPart

    f = open(fileName, 'w')
    f.write(html)
    f.close()
