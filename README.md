# Contao 4 - Demo Bundle


Add Bundle as Git-Repository to Composer<br>
add code to \<contao root path\>/composer.json
```json
{
    ...,
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/jodermo/petzka-demo-bundle.git"
        }
    ],
    "require": {
        ...,
        "petzka/demo-bundle": "master"
    },
    ...
}
```

## local configuration

### Windows 10 + XAMPP:

#### PHP configuration
change following lines in php.ini

```
file_uploads = On (ca. Zeile 833)
upload_temp_dir = "C:\Xampp\tmp" (ca. Zeile 838)
upload_max_filesize = 256M (ca. Zeile 842)
allow_url_fopen = On (ca. Zeile 853)
max_execution_time = 360 (ca. Zeile 386)
memory_limit = -1 (ca. Zeile 407)
post_max_size = 128M (ca. Zeile 690)
extension=intl (ca. Zeile 917) das Semikolon wegnehmen
extension=soap (ca. Zeile 939) das Semikolon wegnehmen
```



#### PHP extension "ssh2"
Download: https://windows.php.net/downloads/pecl/releases/ssh2/0.12/

add following line to php.ini
```
extension=php_ssh2.dll
```


#### Add Virtual Host
add following code to <b>httpd-vhosts.conf</b><br>
(in \<xampp path\>/apache/conf/extra/)
```
<VirtualHost *:80>
  DocumentRoot "<project path>\web"
  ServerName contao-demo
  <Directory "<project path>\web">
    Options +FollowSymlinks
    AllowOverride All
    Require all granted
  </Directory>

  ErrorLog "D:\xampp\apache\logs\contao-demo_error.log"
  CustomLog "D:\xampp\apache\logs\contao-demo_access.log" combined
</VirtualHost>
```


