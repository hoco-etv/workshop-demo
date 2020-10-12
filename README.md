# klushok-site

## Ideas:

1. Shopping cart with live preview of the items added via search query and web preview  
    - RS search query: https://nl.rs-online.com/web/c/?sra=oss&r=t&searchTerm=<RS-PROD-NO||search query>
    - Farnell queary: nl.farnell.com/<FARNELL-PROD-NO> , also API available
2. carboard like view
    - [preview](https://www.google.com/search?client=firefox-b-d&tbm=isch&sa=1&ei=UI93XbHgNeL5qwGp6bLYDQ&q=php+shopping+cart&oq=php+shoppi&gs_l=img.3.0.0i19.12604.14060..14925...0.0..0.138.734.9j1......0....1..gws-wiz-img.......0j0i67j0i10j0i30j0i8i30i19._1xfaw8Zyu8#imgrc=vhOXnRJDWv5gMM:)
    - [information](https://phppot.com/php/simple-php-shopping-cart/)
3. Is het stuk pagina 

4. Add email adress to be notified if the status of the device changes back to 'OK' or 'Decomissioned', this might be a separate DB notifier emails where everyone who'd like to be notified if components or devices get updated can submit to those components/devices


## dingen waar ik tegenaan liep
1. Classes en Controllers MOETEN met een hoofdletter, er gaat meer stuk dan je denkt.
2. Post en get requests moeten toegevoegd worden in de behaviour van een class
3. Een db table die je lokaal maakt, moet middels een migration toegevoegd worden bij deployment
4. Statisch vs niet-statische methods aanroepen in docker
5. 'make suggestion' button for new devices (and possibly a poll to vote on suggestions), this could also be an email