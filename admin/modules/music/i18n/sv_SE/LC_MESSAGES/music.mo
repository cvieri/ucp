��    4      �  G   \      x     y     �  �   �     I     V  V   k     �  	   �     �     �     �               1     K     S     g     �     �     �     �     �     �     �     �  /    :   <  "   w     �  ?   �     �     	  �  	  =   �
  �   �
     �     �  P   �  
   
          !     -  
   9  
   D  
   O     Z  >  l  _   �            J     a  a     �     �  �   �     �     �  S   �       	        (     4     L     k     s     �  	   �  #   �     �     �  !        -     K  	   `     j  	   |     �  $  �  a   �  !        @  /   [     �     �  �  �  F   f  �   �  	   h  !   r  ]   �  	   �  
   �  
     
     	     	   '  	   1     ;  F  I  O   �     �     �  [   �            '   /                                      3         1                   !   -   ,          *   2          %                                
   #   	          +                 0          4   (      .   &          )      $             "          Add Music Category Add Streaming Category Allows you to Set up Different Categories for music on hold.  This is useful if you would like to specify different Hold Music or Commercials for various ACD Queues. Application: Cannot write to file Categories: \"none\" and \"default\" are reserved names. Please enter a different name Category Name: Category: Check Completed processing Convert Music Files to WAV Delete Delete Music Category %s Delete Streaming Category Deleted Disable Random Play Do not encode wav to mp3 Edit Streaming Category Enable Random Play Error Deleting %s Error Processing Music on Hold No file provided On Hold Music Optional Format: Optional value for "format=" line used to provide the format to Asterisk. This should be a format understood by Asterisk such as ulaw, and is specific to the streaming application you are using. See information on musiconhold.conf configuration for different audio and Internet streaming source options. Please enter a streaming application command and arguments Please enter a valid Category Name Please select a file to upload Please wait until the page loads. Your file is being processed. Submit Changes System Setup The volume adjustment is a linear value. Since loudness is logarithmic, the linear level will be less of an adjustment. You should test out the installed music to assure it is at the correct volume. This feature will convert MP3 files to WAV files. If you do not have mpg123 installed, you can set the parameter: <strong>Convert Music Files to WAV</strong> to false in Advanced Settings This is not a fatal error, your Music on Hold may still work. This is the "application=" line used to provide the streaming details to Asterisk. See information on musiconhold.conf configuration for different audio and Internet streaming source options. Upload Upload a .wav or .mp3 file: Uploading and management of sound files (wav, mp3) to be used for on-hold music. Volume 10% Volume 100% Volume 125% Volume 150% Volume 25% Volume 50% Volume 75% Volume Adjustment When set to false, the MP3 files can be loaded and WAV files converted to MP3 in the MoH module. The default behavior of true assumes you have mpg123 loaded as well as sox and will convert MP3 files to WAV. This is highly recommended as MP3 files heavily tax the system and can cause instability on a busy phone system You must have at least one file for On Hold Music.  Please upload one before deleting this one. default in sox failed to convert file and original could not be copied as a fall back Project-Id-Version: FreePBX musik
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2011-09-23 09:52+0000
PO-Revision-Date: 2011-03-20 00:00+0100
Last-Translator: Mikael Carlsson <mickecamino@gmail.com>
Language-Team: 
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Poedit-Language: Swedish
X-Poedit-Country: SWEDEN
 Lägg till musikkategori Lägg till strömkategori Ger dig möjlighet att sätta upp olika kategorier för pausmusik. Detta är användbart för att specificera olika typer av pausmusik eller reklam för köer. Applikation: Kan inte skriva till fil Kategorierna \"none\" och \"default\" är reserverade namn. Skriv in ett annat namn Kategorinamn: Kategori: Kontrollera Färdig med bearbetning Komvertera musikfiler till WAV Ta bort Ta bort kategorin %s Ta bort strömmande kategori Borttagen Avaktivera slumpmässig uppspelning Koda inte om wav till mp3 Redigera strömmande kategori Aktivera slumpmässig uppspelning Fel uppstod när %s togs bort Fel vid hantering av Pausmusik Ingen fil angiven Pausmusik Valfritt format: Valfritt värde för raden "format=", används av Asterisk och måste vara ett format som Asterisk förstår, t.ex. ulaw, denna rad används specifikt för den applikation av strömmande ljud du anger. Titta i filen musiconhold.conf för olika ljudformat och strömmande media från Internet. Skriv in en applikation för strömmande media och eventuella argument denna applikation behöver Skriv in ett giltigt kategorinamn Välj en fil att ladda upp Vänta medan sidan laddas och din fil hanteras. Spara ändringar Systeminställningar Justering av volymen anges som ett linjärt värde. Eftersom loudness är logaritmiskt kommer den linjära nivån att vara en mindre justering. Du bör testa den installerade musiken för att försäkra dig om det rätta värdet. Detta funktion kommer att konvertera filerna MP3 till WAV. Om du inte har installerat mpg123 kan du sätta parametern: <strong>Konvertera musikfiler till WAV</strong> till falskt i Avancerade inställningar Detta är inget kritiskt fel, din pausmusik kommer kanske att fungera. Detta är raden för "application=" som används för att förse Asterisk med strömmande ljud. Titta i filen musiconhold.conf för olika ljudformat och strömmande media från Internet. Ladda upp Ladda upp en .wav eller .mp3 fil: Modul för att ladda upp och underhålla ljudfiler (wav, mp3) för användning till pausmusik 10% volym 100% volym 125% volym 150% volym 25% volym 50% volym 75% volym Justera volym Om denna inställning är falsk kam MP3 filer laddas och WAV-filer konverteras till MP3 i Pausmusik-modulen. Standardvalet, sant, förutsätter att du har installerat både mpg123 och sox som kommer att komvertera MP3 till WAV. Detta är startkt rekommenderat då MP3-filer belastar systemet och kan orsaka stabilitetsproblem. Du måste minst ha en fil för pausmusik. Ladda upp en före du tar bort denna. default i sox kunde inte konvertera filen och originalfilen kunde inte kopieras som en reservlösning 