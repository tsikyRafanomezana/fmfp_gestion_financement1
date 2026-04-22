<?php

namespace App\Form;

use App\Entity\DemandeFinancement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DemandeFinancementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEditing = $options['is_editing'] ?? false;

        if ($isEditing) {
            $builder->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'EN_ATTENTE',
                    'Validée' => 'VALIDEE',
                    'Rejetée' => 'REJETEE',
                ],
                'required' => true,
            ]);
        }
        else{
            $builder
            ->add('matricule',null,[
                'label'=> "Matricule de l'entreprise"
            ])
            ->add('nom', null, [
                'label' => "Nom de l'entreprise",
                
            ])
            ->add('intitule',null,[
                'label'=> "Intitulé du projet"
            ])
            ->add('montantDemande',NumberType::class,[
                'label'=> "Montant demandé"
            ]);
        }

        $builder->add('valider', SubmitType::class, [
            'label' => 'Valider'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeFinancement::class,
            'is_editing' => false,
        ]);
    }
}
