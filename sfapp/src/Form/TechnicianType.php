<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type for Technician options.
 *
 * This form consists of 4 checkboxes that allow the user to select options.
 * All checkboxes are optional and hidden from the user interface by default
 * (using inline CSS with `display: none;`).
 *
 * @extends AbstractType<array<string, mixed>>
 */
class TechnicianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkbox1', CheckboxType::class, [
                'label' => 'Option 1', // Label for the first checkbox
                'required' => false, // The checkbox is optional
                'attr' => [
                    'style' => 'display: none;', // Hides the checkbox from the UI
                ],
            ])
            ->add('checkbox2', CheckboxType::class, [
                'label' => 'Option 2', // Label for the second checkbox
                'required' => false, // The checkbox is optional
                'attr' => [
                    'style' => 'display: none;', // Hides the checkbox from the UI
                ],
            ])
            ->add('checkbox3', CheckboxType::class, [
                'label' => 'Option 3', // Label for the third checkbox
                'required' => false, // The checkbox is optional
                'attr' => [
                    'style' => 'display: none;', // Hides the checkbox from the UI
                ],
            ])
            ->add('checkbox4', CheckboxType::class, [
                'label' => 'Option 4', // Label for the fourth checkbox
                'required' => false, // The checkbox is optional
                'attr' => [
                    'style' => 'display: none;', // Hides the checkbox from the UI
                ],
            ]);
    }

    /**
     * Configures the options for this form type.
     *
     * The `data_class` option is set to `null`, indicating that this form does not
     * bind to any specific data model, and it is only used for the checkbox options.
     *
     * @param OptionsResolver $resolver The options resolver instance
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // No associated data class for this form
        ]);
    }
}