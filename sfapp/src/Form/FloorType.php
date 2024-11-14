<?php
#############################################################################################
## @Name of file : FloorType.php                                                           ##
## @brief : Form type for floor entity                                                     ##
## @Function : Defines the form fields for creating or editing a floor                     ##
####                                                                                       ##
## Uses Symfony form component to generate floor form fields, including building selection ##
##                                                                                         ##
#############################################################################################

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FloorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberFloor')
            ->add('IdBuilding', EntityType::class, [
                'class' => Building::class,
'choice_label' => 'NameBuilding',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Floor::class,
        ]);
    }
}
