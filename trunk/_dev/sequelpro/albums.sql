SELECT count(*)
FROM albums t1, album_artist_lookup t2, band_lookup t3
WHERE (t3.`bandid`=t2.`artistid` AND t3.`artistid`=6 AND t1.`id`=t2.`albumid`)

SELECT count(*) 
FROM albums t1, album_artist_lookup t2
WHERE (t1.id=t2.albumid AND t2.artistid=6) 
;