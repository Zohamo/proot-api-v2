<?php

namespace Core;

/**
 * Entité de base, contient les outils nécéssaires pour alimenter
 * et récupérer les données d'un objet.
 */
abstract class AbstractEntity
{
    /**
     * Noms des propriétés de cette entité correspondant aux entités imbriquées.
     *
     * @var string[]
     */
    protected $nestedEntities = [];

    /**
     * Définit les propriétés de l'entité.
     *
     * @param  mixed[] $data
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    /**
     * Définit les propriétés de l'entité.
     *
     * @param  mixed[] $data Tableau associatif de données
     * @return $this
     */
    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
        return $this;
    }

    /**
     * Méthode magique pour récupérer la valeur d'une propriété.
     *
     * On recherche d'abord si une méthode `$name()` existe,
     * ensuite si une propriété `$name` existe.
     *
     * @param  string $name Nom de la propriété
     * @return mixed Valeur de la propriété
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        } elseif (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Méthode magique pour définir la valeur d'une propriété.
     *
     * Si une méthode du type `setNom_de_la_propriété()` n'existe pas,
     * on définit directement la propriété `$name` si elle existe.
     *
     * @param  string $name  Nom de la propriété
     * @param  mixed  $value Valeur de la propriété
     * @return void
     */
    public function __set($name, $value)
    {
        $setter = "set" . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            if (!empty($this->nestedEntities)) {
                foreach ($this->nestedEntities as $entName) {
                    if (method_exists($this->$entName, $setter)) {
                        $this->$entName->$setter($value);
                    } elseif (property_exists($this->$entName, $name)) {
                        $this->$entName->$name = $value;
                    }
                }
            }
        }
    }
}
