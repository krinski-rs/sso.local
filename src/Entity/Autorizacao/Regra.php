<?php

namespace App\Entity\Autorizacao;

/**
 * Regra
 */
class Regra
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
    private $ativo = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $idUsuarios;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUsuarios = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Regra
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
     * @return Regra
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
     * Add idUsuario.
     *
     * @param \App\Entity\Autorizacao\Usuario $idUsuario
     *
     * @return Regra
     */
    public function addIdUsuario(\App\Entity\Autorizacao\Usuario $idUsuario)
    {
        $this->idUsuarios[] = $idUsuario;

        return $this;
    }

    /**
     * Remove idUsuario.
     *
     * @param \App\Entity\Autorizacao\Usuario $idUsuario
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdUsuario(\App\Entity\Autorizacao\Usuario $idUsuario)
    {
        return $this->idUsuarios->removeElement($idUsuario);
    }

    /**
     * Get idUsuarios.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUsuarios()
    {
        return $this->idUsuarios;
    }
}
