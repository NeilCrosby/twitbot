# TwitBot

TwitBot is a library intended to allow easy creation of Bots using the Twitter website.  I gave [a talk about TwitBot][1] at BarCamp Brighton 2.

## Types of TwitBot

### RSS TwitBot

The simplest type of bot.  This type simply watches a given RSS feed and outputs new feed entries as a new tweet by a given username.

This bot will tweet at most once per 15 minute interval.  If multiple items have been added to the watched RSS feed since the last tweet then the bot will not tweet each item, but will instead tweet a single message along the lines of "$num new items posted to $link since the last time I tweeted".

The RSS TwitBot is currently used by [ttwr][2] to follow the latest reviews feed on [The Ten Word Review][3].

### Echo TwitBot

Accepts direct messages from people who it follows and retweets them.  This allows the creation of "Twitter Channels".

The Echo TwitBot is currently used by [yahoofood][4] to alert London-based Yahoos of tasty food in the vicinity.

### Question TwitBot

The most exciting type of bot.  This bot watches for direct messages, then passes them through to an external web service.  It can then pass any response from the webservice back to the user who initially sent it the message.

This bot type is currently in flux and could conceivably change a lot - it has a lot of scope for what it could do.

The Question TwitBot is currently used by [ttwr][2] to allow a few alpha testers to tweet reviews to [The Ten Word Review][3].


[1]: http://www.slideshare.net/neilcrosby/twitter-bots
[2]: http://twitter.com/ttwr
[3]: http://thetenwordreview.com
[4]: http://twitter.com/yahoofood
