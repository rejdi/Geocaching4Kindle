vlastny geokindle book

co by to mohlo vediet?

vstupy:
* cookie
alebo
* username/password

1. z vyhladavania
vstup:
* suradnice
* pocet kesi / max vzdialenost
* filter na typ kese
* filter na obtiaznost
* filter na teren
* ci som este nenasiel
* ci cache existuje
* typ kese

2. zo zoznamu kesi
vstup:
* zoznam kesi (geocodes)

vystupy:
* vyrobi kindle ebook, kde si clovek aj povie co tam vsetko chce mat
* vyrobi gpx
* vyrobi gpi

technologia:
hlavny kod by bolo php, co bude robit aj webovy vstup / vystup
kindlegen - generovanie vystupu pre kindle z html
xslt sablony na preparsovanie vstupu z webu, vyhodenie somarin, generovanie poi, generovanie gpx
wget na masove stiahnutie html + obrazkov

treba doplnit

- cas vytvorenia kese (ten este skonvertovat)
- waypoints
  - pozicia XX.YYYY
- selectovanie na typ mapy (mapnik, freemap, ...)
