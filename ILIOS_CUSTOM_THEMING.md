# Custom Theming in Ilios

While the Ilios Project team is working on better ways to accomplish theme changes within the Ilios interface directly, we have provided a workaround for administrators who manage their own Ilios 3 API backend server and would like to change a few details of the look and feel of their site to more appropriately reflect the theme/branding of their respective institution.

Even though Ilios v3 client code is now served to users from Amazon S3 servers in "the cloud", customizing the CSS styling of the frontend Ilios GUI is still possible by adding custom CSS to the file at [web/theme-overrides/custom.css](https://github.com/ilios/ilios/tree/master/web/theme-overrides/custom.css).

The following steps specifically outline the process for changing the color of the main header and the Ilios logo graphic to something other than the defaults, but other themed attributes of Ilios can also be changed using this same method.

## BEFORE YOU BEGIN

For this excercise, you will need to know what color you would like your main header "banner" to be and you will need a custom logo graphic sized the same as the default Ilios logo graphic.  You should also be somewhat familiar with CSS, but it's not super important if you are just following these instructions to update the banner color and its logo.

#### The Main Header Color

By default, the header that spans the top of every screen in Ilios has a background-color attribute that we on the Ilios team refer to as 'ilios-orange' or '#cc6600' (in hexadecimal). In the [web/theme-overrides/custom.css](https://github.com/ilios/ilios/tree/master/web/theme-overrides/custom.css) file, however, we have the custom style declaration set to '#ff1493' and commented out by default.  To see your instance of Ilios with a horrendous pink banner across the top, just uncomment this line, save the file, and clear your cache!  If you want a color other than 'horrendous pink', just follow the steps below:

#### The Main Header Logo Graphic

Currently, the [default Ilios logo](https://github.com/ilios/frontend/blob/master/public/assets/images/ilios-logo.png), as it is served from Amazon S3, is 84px wide and 42px tall and the source file can be found at [https://github.com/ilios/frontend/blob/master/public/assets/images/ilios-logo.png](https://github.com/ilios/frontend/blob/master/public/assets/images/ilios-logo.png).  If you would like to use your own institution's logo instead, you will want to create/use a logo image with the same height and width as the default Ilios logo.  You can customize the size of the logo later if you like but, for the sake of easily understanding how this process works, you should leave it the same size for now.

Once you have the logo you would like to use, you will want to copy it to your Ilios 3 API server, into the ['web/theme-overrides/'](https://github.com/ilios/ilios/tree/master/web/theme-overrides/) subfolder of the Ilios application.  Once it is uploaded, you will need to update the filename in the [custom.css](https://github.com/ilios/ilios/tree/master/web/theme-overrides/custom.css) stylesheet to specify its proper filename.

### Changing the color and logo of the main header

In addition to changing the logo in the header of the Ilios application, you may want to change the color of the header banner as well.

To do this, just change value of the header's 'background-color' attribute in the ['custom.css'](https://github.com/ilios/ilios/tree/master/web/theme-overrides/custom.css) theme style override file.

To change the header color, find the line that reads:

```//background-color: #ff1493;```

and uncomment the style declaration.  Then explicitly change the '#ff1493' value to the desired new hexadecimal color (eg, '#54bfe2'). After removing the comment lines and making the change, the line should now look like this:

```background-color: #54bfe2;```

To point the logo graphic source to the one with the new filename, make sure you have uploaded your new file (eg, 'custom_logo.png') to the 'web/theme-overrides/' folder on your API server and then find the line in 'custom.css' that reads:

```//background-image: url('custom_logo.png');```

and change it so that the comment lines are removed and the filename matches that of the image you just uploaded, like so:

```background-image: url('custom_logo.png');```

Once you make these changes, save the file(s) on the API server, and clear your Symfony cache, refreshing your application frontend should automatically reflect the new changes!

### Congratulations! You've now updated your Ilios theme!
