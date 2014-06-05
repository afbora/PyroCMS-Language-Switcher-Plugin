PyroCMS-Language-Switcher-Plugin
================================

Language switcher plugin allow to display public site languages as text, image or anyway you like and allow users to change current language.

This plugin doesnt provide "Google Translate" service. Just allow users to change site language that you selected from settings.

Usage:

<code>

     <ul class="dropdown-menu">
     
          {{ language:switcher mode="png" }}
          
          <li><a href="{{ link }}">{{ img }} {{ name }}</a></li>
     
          {{ /language:switcher }}
     
     </ul>
     
</code>
