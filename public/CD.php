<?php
class CD
{
    private $genre;
    private $titre;
    private $author;
    private $price;
    private $image;

    /**
     * @param $genre
     * @param $titre
     * @param $author
     * @param $price
     * @param $image
     */
    public function __construct($genre, $titre, $author, $price, $image)
    {
        $this->genre = $genre;
        $this->titre = $titre;
        $this->author = $author;
        $this->price = $price;
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    public function toString()
    {
        echo "Auteur : " . $this->getAuthor() . "</br>";
        echo "Genre  : " . $this->getGenre() . "</br>";
        echo "Titre  : " . $this->getTitre() . "</br>";
        echo "Prix   : " . $this->getPrice() . "</br>";
    }
}
$cd1 = new CD("Rap", "Titre 1", "tom", 40, "image");
$cd2 = new CD("Rock", "Titre 2", "tomat", 230, "image2");
$cd3 = new CD("n Roll", "Titre 3", "tohma", 20, "image3");
$cd4 = new CD("Jazz", "Titre 4", "Toma", 4, "image4");
$cd5 = new CD("House", "Titre 5", "Thomas", 10, "image5");