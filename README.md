> [!CAUTION]
> Preview-Version! An vielen Stellen wird's vermutlich Fehler geben.

# Musik, Bruder! - Dein Freund für deine Musik

**Musik, Bruder!** ist dein bester Freund, wenn du selbst einiges an lokaler
Musik besitzt und keine Lust mehr hast, Spotify jeden Monat 10
Euro zu spendieren. Hiermit kannst du deine Musik, eigene Bilder
für Alben, Künstler und sogar Videos (wie bei Spotify) zu
einzelnen Tracks hochladen (WOW!)!

Eine kleine Preview gefällig? Bitteschön:

https://github.com/user-attachments/assets/a451f78d-d819-4bec-8dd2-4c5f7b30a982

<br>

<p align="center">…………… 🫴 ……………</p>

## Setup - Step (〰️) by Step (〰️)

### 〰️ **1 · Abhängigkeiten installieren** ¬

Ein paar Dinge müssen auf deinem `Linux`-System vorhanden sein,
damit du durchstarten kannst. Hierzu zählen `php` 8.5 und `ncurses`.
Wie man das installiert, musst du selbst herausfinden 💋

### 〰️ **2 · Setup ausführen** ¬

Um deinen besten Freund der Musik zu installieren, gib einfach
folgenden Befehl in dein Terminal ein:

```bash
bash <(curl -fsSL https://www.heia.kim/MusikBruder/setup.sh)
```

Was das Script macht, kannst du in diesem Repo in der Datei
`setup.sh` einsehen. Die Datei auf `www.heia.kim` ist immer die
aktuellste Version des Setups in diesem Repo!

### 〰️ **9 · Fertig** ¬

Ab gehts. Dein Freund ist unter `http://localhost:6789`
erreichbar.

<br>
<p align="center">…………… ❓ ……………</p>

### **Wo ist die App gespeichert?**

Das Setup-Script sollte, wenn alles glatt gelaufen ist, alles in
`~/.local/share/musikbruder` gespeichert haben.
<br>

### **Meine Musik? Wohin?**

Deine Musik kannst du in
`~/.local/share/musikbruder/public/data/user/1/tracks` kopieren.
Neue Songs werden immer automatisch bei jedem Seiten-Reload
synchronisiert. Um alles einfacher zu machen, kannst du auch einen
Symlink zu deinem lokalen Musik-Ordner erstellen (oder wo du auch
immer deine Musik gespeichert hast). Das geht so:

```BASH
ln -s ~/Music ~/.local/share/musikbruder/public/data/user/1/tracks
```

<br>

<p align="center">…………… 🍃 ……………</p>

### Philosophie

Ich arbeite gerne und viel an diesem Projekt, aber so lange die
Abende voll Erfüllung und Zufriedenheit auch werden, so schleichen
sich doch auch einiges an Fehlern ein. Deswegen solltest Du damit
rechnen, das nicht immer alles glatt läuft! Das ist der Preis, den
wir statt den 10 Euro monatlich zahlen.
<br><br>

<p align="center">…………… 👀 ……………</p>

<p align="center">Mit viel ☕, höchst verfügbarer ❤️ und einem großen 🍆 gebaut!
Ich übernehme keine Verantwortung für Over-Engineering und/oder
schlechte Performance 😘.</p>

<br>

<p align="center" style="font-weight:bold">2026 &copy; Justin Seidel. Alle Rechte vorbehalten.</p>
