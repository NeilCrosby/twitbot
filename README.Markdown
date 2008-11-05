# TwitBot

TwitBot is a library intended to allow easy creation of Bots using the Twitter website.  I gave [a talk about TwitBot][1] at BarCamp Brighton 2.

## Types of Bot

### RSS Bot

The simplest type of bot.  This type simply watches a given RSS feed and outputs new feed entries as a new tweet by a given username.

### Echo Bot

Accepts direct messages from people who it follows and retweets them.  This allows the creation of "Twitter Channels".

### Question Bot

The most exciting type of bot.  This bot watches for direct messages, then passes them through to an external web service.  It can then pass any response from the webservice back to the user who initially sent it the message.

This bot type is currently in flux and could conceivably change a lot - it has a lot of scope for what it could do.

[1]: http://www.slideshare.net/neilcrosby/twitter-bots