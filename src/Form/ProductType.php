<?php

namespace App\Form;

use App\DTO\ProductDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nome',
                'invalid_message' => 'Insira um texto!',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Informe o nome'
                    ])
                ]
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'label' => 'Preco',
                'invalid_message' => 'Insira um numero!',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Informe o valor'
                    ])
                ],
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
                'label' => 'Quantidade',
                'invalid_message' => 'Insira um numero!',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Informe a quantidade'
                    ])
                ],
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['edit'] ? 'Salvar' : 'Criar',
                'attr' => [
                    'class' => 'btn-secondary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductDTO::class,
            'edit' => false
        ]);
    }
}
