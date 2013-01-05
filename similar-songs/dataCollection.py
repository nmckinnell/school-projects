# dataCollection.py
# 02_coding for CS349A
# Author: Nora McKinnell
# Feb 11, 2012

import urllib2
import json
from urllib import urlencode
import os

API_KEY = "045c83ccc1ed8d0d7af175299196fc4b"

# Get track info via specified action on last.fm API
def getTracks(action,trackName,artistName = ''):

    # Encode inputs for URL query
    f = { 'track' : trackName }
    if artistName != '':
        f['artist'] = artistName
    
    URL = "http://ws.audioscrobbler.com/2.0/"
    method = "?method=%s&" % action
    parameters = urlencode(f)
    parameters += "&format=json"
    apikey = "&api_key=" + API_KEY

    # Combine all parts together to create the full URL
    request = URL + method + parameters + apikey
    # print request

    # Send request and read the returned result
    data = urllib2.urlopen(request).read()
    # print type(data)
    # print len(data)

    # Convert to json
    dataJson = json.loads(data) # load from string
    return dataJson

def storeJson(dataJson, fileName = 'search_results'):
    f = open('%s.json' % fileName, 'w') # open file for writing
    json.dump(dataJson, f) # this performs a data dump into the file
    f.close()

    dataDct = json.load(open('%s.json' % fileName)) # file is open for reading
    # print type(dataDct)
    # print dataDct.keys() # show keys
    return dataDct

def getTopSimilar(trackName,trackArtist):
    allSimilar = getTracks('track.getsimilar',trackName,trackArtist)
    usedArtists = [trackArtist] # keep track of already used artists
    topFive = [] # store top five similar tracks
    i = 0
    while len(topFive) < 5:
        nextSimilar = allSimilar['similartracks']['track'][i]
        # Check to see if not already used and has image
        if usedArtists.count(nextSimilar['artist']['name']) == 0 and "image" in nextSimilar:
            topFive.append(nextSimilar)
        usedArtists.append(nextSimilar['artist']['name'])
        i += 1
    storeJson(topFive,'similar-tracks')

    for i in range(0,5):
        trackName = topFive[i]['name']
        trackArtist = topFive[i]['artist']['name']
        # print topFive[i]
        # print "preparing to make file for " + str(trackArtist) + " - " + str(trackName)
        trackInfo = storeJson(getTracks('track.getinfo',trackName,trackArtist),'similar-%s' % i)
        # print "completed: " + str(trackInfo)
                
def start():

    trackName = raw_input("Enter a track name: ")
    
    dataJson = getTracks('track.search',trackName)
    # dataDct = storeJson(dataJson)
    
    trackName = dataJson['results']['trackmatches']['track'][0]['name']
    trackArtist = dataJson['results']['trackmatches']['track'][0]['artist']
    print "Getting information for " + trackArtist + " - " + trackName + " ... "
    trackInfo = storeJson(getTracks('track.getinfo',trackName,trackArtist),'orig-track')
    print "Getting similar tracks ... "    
    getTopSimilar(trackName,trackArtist)

if __name__=="__main__":
    start()
    from musicPlayer import *
    tracks = getSimilarInfo('similar-tracks.json')
    # Create page for main user-inputted track
    write_HTML_file('orig-track.json','album-main.html',tracks)
    # Create pages for similar tracks
    for i in range(0,5):
        write_HTML_file('similar-' + str(i) + '.json','album-' + str(i) + '.html')

