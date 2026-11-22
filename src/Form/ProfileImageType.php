<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profileImage', FileType::class, [
                'label' => 'Profile image (JPG or PNG file)',
                'mapped' => false,
                'required' => false,
                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new Assert\File(
                        maxSize: '1024K',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png'
                        ],
                        mimeTypesMessage: 'Please upload a valid {{ types }} image'
                    
                    )
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
