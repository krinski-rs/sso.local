<?php

namespace App\Entity\Autorizacao;

/**
 * Menu
 */
class Menu
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var bool
     */
    private $ativo;

    /**
     * @var int
     */
    private $lft;

    /**
     * @var int
     */
    private $rgt;

    /**
     * @var \App\Entity\Autorizacao\Menu
     */
    private $menu;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nome.
     *
     * @param string $nome
     *
     * @return Menu
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set ativo.
     *
     * @param bool $ativo
     *
     * @return Menu
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

        return $this;
    }

    /**
     * Get ativo.
     *
     * @return bool
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Set lft.
     *
     * @param int $lft
     *
     * @return Menu
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft.
     *
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt.
     *
     * @param int $rgt
     *
     * @return Menu
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt.
     *
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set menu.
     *
     * @param \App\Entity\Autorizacao\Menu|null $menu
     *
     * @return Menu
     */
    public function setMenu(\App\Entity\Autorizacao\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu.
     *
     * @return \App\Entity\Autorizacao\Menu|null
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
