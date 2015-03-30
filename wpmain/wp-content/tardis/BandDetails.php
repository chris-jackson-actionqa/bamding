<?php

/*
 * Band's Details
 * Each user can only have one band (at the moment). 
 */

/**
 * Band Details class
 *
 * @author Seth
 */
class BandDetails {
    private $oConn = null;
    private $sUserLogin = '';
    private $isSolo = FALSE;
    private $bandName = "";
    private $genre = "";
    private $soundsLike = "";
    private $email = "";
    private $website = "";
    private $music = "";
    private $phone = "";
    private $draw = "";
    private $video = "";
    private $calendar = "";
    private $sites = "";

    /**
     * Constructor. Connects to the database. 
     * @param string $sUserLogin
     * @throws InvalidArgumentException
     */
    public function __construct($sUserLogin)
    {
      if(empty($sUserLogin))
      {
        throw new InvalidArgumentException(
                'Band Details requires valid user login');
      }

      $this->sUserLogin = $sUserLogin;
      $oDB = new Database();
      $this->oConn = $oDB->connect();
      $this->retrieve();
    }
    
    /**
     * Destructor. Closes database connection.
     */
    public function __destruct() {
        $this->oConn->close();
    }
    
    /**
     * Updates or inserts band details.
     * @throws RuntimeException if fails to update/insert
     */
    public function update()
    {
        $sql = <<<SQL
INSERT INTO band_details
  (
  user_login,
  band_name,
  solo,
  genre,
  sounds_like,
  email,
  website,
  music_page,
  phone,
  draw,
  live_video_page,
  calendar_page,
  sites
  )
VALUES
  (
  '{$this->sUserLogin}',
  '{$this->bandName}',
  '{$this->isSolo}',
  '{$this->genre}',
  '{$this->soundsLike}',
  '{$this->email}',
  '{$this->website}',
  '{$this->music}',
  '{$this->phone}',
  '{$this->draw}',
  '{$this->video}',
  '{$this->calendar}',
  '{$this->sites}'
  )
ON DUPLICATE KEY UPDATE
  user_login='{$this->sUserLogin}',
  band_name='{$this->bandName}',
  solo='{$this->isSolo}',
  genre='{$this->genre}',
  sounds_like='{$this->soundsLike}',
  email='{$this->email}',
  website='{$this->website}',
  music_page='{$this->music}',
  phone='{$this->phone}',
  draw='{$this->draw}',
  live_video_page='{$this->video}',
  calendar_page='{$this->calendar}',
  sites='{$this->sites}'
SQL;
        
        $mResult = $this->oConn->query($sql);
    
        if(FALSE === $mResult)
        {
          throw new RuntimeException("Band Details Update Error: " . 
                  $this->oConn->error);
        }
    }
    
    /**
     * Retrieve band details from database
     * @return type
     */
    private function retrieve()
    {
        $sql = <<<SQL
SELECT * FROM band_details
WHERE user_login='{$this->sUserLogin}'
SQL;

        $result = $this->oConn->query($sql);
        
        if(empty($result))
        {
            return;
        }
        
        $rows = Database::fetch_all($result);
        foreach( $rows as $row)
        {
            $this->bandName = $row['band_name'];
            $this->isSolo = (bool)$row['solo'];
            $this->genre = $row['genre'];
            $this->soundsLike = $row['sounds_like'];
            $this->email = $row['email'];
            $this->website = $row['website'];
            $this->music = $row['music_page'];
            $this->phone = $row['phone'];
            $this->draw = $row['draw'];
            $this->video = $row['live_video_page'];
            $this->calendar = $row['calendar_page'];
            $this->sites = $row['sites'];
        }
    }
    
    /**
     * Set Band Name
     * @param string $bandName
     */
    public function setBandName($bandName)
    {
        $this->bandName = $this->oConn->escape_string($bandName);
    }
    
    /**
     * Get the band name
     * @return string band name
     */
    public function getBandName()
    {
        return stripslashes($this->bandName);
    }
    
    /**
     * Set if this is a solo artist
     * @param bool $isSolo
     */
    public function setSolo($isSolo)
    {
        $this->isSolo = (bool)$isSolo;
    }
    
    /**
     * Get if its a solo artist
     * @return bool
     */
    public function getSolo()
    {
        return (bool)$this->isSolo;
    }
    
    /**
     * Set the genre of music
     * @param string $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $this->oConn->escape_string($genre);
    }
    
    /**
     * Get genre of music
     * @return string
     */
    public function getGenre()
    {
        return stripslashes($this->genre);
    }
    
    /**
     * Set what music this band sounds like
     * @param string $soundsLike
     */
    public function setSoundsLike($soundsLike)
    {
        $this->soundsLike = $this->oConn->escape_string($soundsLike);
    }
    
    /**
     * Get what this band sounds like
     * @return string
     */
    public function getSoundsLike()
    {
        return stripslashes($this->soundsLike);
    }
    
    /**
     * Set the booking email for the band
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $this->oConn->escape_string($email);
    }
    
    /**
     * Get band booking email
     * @return string
     */
    public function getEmail()
    {
        return stripslashes($this->email);
    }
    
    /**
     * Set the band's main website
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $this->oConn->escape_string($website);
    }
    
    /**
     * Get band website
     * @return string
     */
    public function getWebsite()
    {
        return stripslashes($this->website);
    }
    
    /**
     * Set the band's music page
     * @param string $music
     */
    public function setMusic($music)
    {
        $this->music = $this->oConn->escape_string($music);
    }
    
    /**
     * Get band music page
     * @return string
     */
    public function getMusic()
    {
        return stripslashes($this->music);
    }
    
    /**
     * Set phone number
     * @param string $music
     */
    public function setPhone($phone)
    {
        $this->phone = $this->oConn->escape_string($phone);
    }
    
    /**
     * Get phone number
     * @return string
     */
    public function getPhone()
    {
        return stripslashes($this->phone);
    }
    
    /**
     * Set draw
     * @param string $draw
     */
    public function setDraw($draw)
    {
        $this->draw = $this->oConn->escape_string($draw);
    }
    
    /**
     * Get draw
     * @return string
     */
    public function getDraw()
    {
        return stripslashes($this->draw);
    }
    
    /**
     * Set video page
     * @param string $video
     */
    public function setVideo($video)
    {
        $this->video = $this->oConn->escape_string($video);
    }
    
    /**
     * Get video
     * @return string
     */
    public function getVideo()
    {
        return stripslashes($this->video);
    }
    
    /**
     * Set calendar page
     * @param string $calendar
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $this->oConn->escape_string($calendar);
    }
    
    /**
     * Get calendar page
     * @return string
     */
    public function getCalendar()
    {
        return stripslashes($this->calendar);
    }
    
    /**
     * Set additional websites
     * @param string $sites
     */
    public function setSites($sites)
    {
        $this->sites = $this->oConn->escape_string($sites);
    }
    
    /**
     * Get sites
     * @return string
     */
    public function getSites()
    {
        return stripslashes($this->sites);
    }
}
