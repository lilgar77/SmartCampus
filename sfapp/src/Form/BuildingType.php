<?php
############################################################################
## @Name of file : BuildingType.php                                       ##
## @brief : Form type for building entity                                 ##
## @Function : Defines the form fields for creating or editing a building ##
####                                                                      ##
## Uses Symfony form component to generate building form fields           ##
##                                                                        ##
############################################################################

namespace App\Form;

use App\Entity\Building;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NameBuilding')
            ->add('AdressBuilding')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
        ]);
    }
}
