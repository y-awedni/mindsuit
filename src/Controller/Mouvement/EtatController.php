<?php

namespace App\Controller\Mouvement;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Etat controller.
 *
 * @Route("etat")
 */
class EtatController extends BaseController {

    public function getTotalEtat($request, $etat, $modeReglement,$mouvement) {
        $em = $this->getEm();
        $qb = $em->getRepository('App\\Entity\\Mouvement')->createQueryBuilder('a');
        $qb->select("SUM(a.ttc) as total_etat");
        $qb->where('a.modeReglement like :modeReglement')->setParameter('modeReglement', $modeReglement);
        $qb->andWhere('a.etat like :etat')->setParameter('etat', $etat);
        $qb->andWhere('a.mouvement like :mouvement')->setParameter('mouvement', $mouvement);
        if ($request->query->get('etat')) {
            $etats = $request->query->get('etat');
            $chaine = "(";
            for ($i = 0; $i < count($etats); $i++) {
                $chaine .= "'" . $etats[$i] . "'";
                if (count($etats) - $i > 1) {
                    $chaine .= ",";
                }
            }
            $chaine .= ")";
            $qb->andWhere('a.etat IN ' . $chaine);
        }
        $mvt_designation = null;
        if ($request->query->get('designation')) {
            $mvt_designation = $request->query->get('designation');
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $mvt_designation . '%');
        }
        $mvt_typeDoc = null;
        if ($request->query->get('typeDoc')) {
            $mvt_typeDoc = $request->query->get('typeDoc');
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $mvt_typeDoc);
        }
        $mvt_mouvement = null;
        $mvt_client = null;
        $mvt_fournisseur = null;
        if ($request->query->get('mouvement')) {
            $mvt_mouvement = $request->query->get('mouvement');
            if ($mvt_mouvement === 'revenu') {
                $qb->andWhere("a.mouvement = 'revenu'");
                if ($request->query->get('client')) {
                    $mvt_client = $request->query->get('client');
                    $qb->andWhere('a.client = :client')->setParameter('client', $mvt_client);
                }
            } else if ($mvt_mouvement === 'depense') {
                $qb->andWhere("a.mouvement = 'depense'");
                if ($request->query->get('fournisseur')) {
                    $mvt_fournisseur = $request->query->get('fournisseur');
                    $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $mvt_fournisseur);
                }
            }
        }

        $mvt_startDateCreation = null;
        $dateFormat = $this->get('app.format_date'); //service for formatting date
        if ($request->query->get('startDateCreation')) {
            $mvt_startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($mvt_startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $mvt_startDateCreation);
            }
        }
        $mvt_endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $mvt_endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($mvt_endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $mvt_endDateCreation);
            }
        }
        $mvt_startDateEcheance = null;
        if ($request->query->get('startDateEcheance')) {
            $mvt_startDateEcheance = $dateFormat->formatDate($request->query->get('startDateEcheance'));
            if ($mvt_startDateEcheance) {
                $qb->andWhere('a.dateEcheance >= :startDateEcheance')->setParameter('startDateEcheance', $mvt_startDateEcheance);
            }
        }
        $mvt_endDateEcheance = null;
        if ($request->query->get('endDateEcheance')) {
            $mvt_endDateEcheance = $dateFormat->formatDate($request->query->get('endDateEcheance'));
            if ($mvt_endDateEcheance) {
                $qb->andWhere('a.dateEcheance <= :endDateEcheance')->setParameter('endDateEcheance', $mvt_endDateEcheance);
            }
        }

        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    /**
     * Lists all mouvement Cheque entities.
     *
     * @Route("/cheque", name="mouvement_cheque_index", methods={"GET"})
     */
    public function chequeAction(Request $request) {
        $em = $this->getEm();
        $qb = $em->getRepository('App\\Entity\\Mouvement')->createQueryBuilder('a');
        $qb->where('a.modeReglement like :modeReglement')->setParameter('modeReglement', 'Chéque');
        if ($request->query->get('etat')) {
            $etats = $request->query->get('etat');
            $chaine = "(";
            for ($i = 0; $i < count($etats); $i++) {
                $chaine .= "'" . $etats[$i] . "'";
                if (count($etats) - $i > 1) {
                    $chaine .= ",";
                }
            }
            $chaine .= ")";
            $qb->andWhere('a.etat IN ' . $chaine);
        }
        $mvt_designation = null;
        if ($request->query->get('designation')) {
            $mvt_designation = $request->query->get('designation');
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $mvt_designation . '%');
        }
        $mvt_typeDoc = null;
        if ($request->query->get('typeDoc')) {
            $mvt_typeDoc = $request->query->get('typeDoc');
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $mvt_typeDoc);
        }
        $mvt_mouvement = null;
        $mvt_client = null;
        $mvt_fournisseur = null;
        if ($request->query->get('mouvement')) {
            $mvt_mouvement = $request->query->get('mouvement');
            if ($mvt_mouvement === 'revenu') {
                $qb->andWhere("a.mouvement = 'revenu'");
                if ($request->query->get('client')) {
                    $mvt_client = $request->query->get('client');
                    $qb->andWhere('a.client = :client')->setParameter('client', $mvt_client);
                }
            } else if ($mvt_mouvement === 'depense') {
                $qb->andWhere("a.mouvement = 'depense'");
                if ($request->query->get('fournisseur')) {
                    $mvt_fournisseur = $request->query->get('fournisseur');
                    $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $mvt_fournisseur);
                }
            }
        }

        $mvt_startDateCreation = null;
        $dateFormat = $this->get('app.format_date'); //service for formatting date
        if ($request->query->get('startDateCreation')) {
            $mvt_startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($mvt_startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $mvt_startDateCreation);
            }
        }
        $mvt_endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $mvt_endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($mvt_endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $mvt_endDateCreation);
            }
        }
        $mvt_startDateEcheance = null;
        if ($request->query->get('startDateEcheance')) {
            $mvt_startDateEcheance = $dateFormat->formatDate($request->query->get('startDateEcheance'));
            if ($mvt_startDateEcheance) {
                $qb->andWhere('a.dateEcheance >= :startDateEcheance')->setParameter('startDateEcheance', $mvt_startDateEcheance);
            }
        }
        $mvt_endDateEcheance = null;
        if ($request->query->get('endDateEcheance')) {
            $mvt_endDateEcheance = $dateFormat->formatDate($request->query->get('endDateEcheance'));
            if ($mvt_endDateEcheance) {
                $qb->andWhere('a.dateEcheance <= :endDateEcheance')->setParameter('endDateEcheance', $mvt_endDateEcheance);
            }
        }

        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $clients = $em->getRepository('App\\Entity\\Client')->findAll();
        $fournisseurs = $em->getRepository('App\\Entity\\Fournisseur')->findAll();

        $total = $qb->select('SUM(a.ttc) as sumDepenses')
                ->getQuery()
                ->getSingleScalarResult();
        $sumRevenus = $qb->andWhere("a.mouvement = 'revenu'")
                ->select('SUM(a.ttc) as sumRevenus')
                ->getQuery()
                ->getSingleScalarResult();


        $sumDepenses = $sumRevenus - $total;
        return $this->render('mouvement/etat/cheque.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients,
                    'fournisseurs' => $fournisseurs,
                    'sumRevenus' => $sumRevenus,
                    'sumDepenses' => - $sumDepenses,
                    'total' => $sumRevenus + $sumDepenses,
                    'totalEnCoursEntrants' => $this->getTotalEtat($request, 'En cours', 'Chéque','revenu'),
                    'totalEnCoursSortants' => $this->getTotalEtat($request, 'En cours', 'Chéque','depense'),
                    'totalEncaissésEntrants' => $this->getTotalEtat($request, 'Encaissé', 'Chéque','revenu'),
                    'totalEncaissésSortants' => $this->getTotalEtat($request, 'Encaissé', 'Chéque','depense'),
                    'totalImpayésEntrants' => $this->getTotalEtat($request, 'Impayé', 'Chéque','revenu'),
                    'totalImpayésSortants' => $this->getTotalEtat($request, 'Impayé', 'Chéque','depense')
        ));
    }

    /**
     * maj cheque
     *
     * @Route("/cheque/traite/maj", name="mouvement_cheque_traite_maj", methods={"POST"})
     */
    public function majChequeAction(Request $request) {
        $mouvementId = $request->request->get('mouvementId');
        $etat = $request->request->get('etat');
        $dateEcheance = $request->request->get('dateEcheance');
        $em = $this->getEm();
        $mouvement = $em->getRepository('App\\Entity\\Mouvement')->findOneById($mouvementId);
        $mouvement->setEtat($etat);
        $date = date_create(str_replace('/', '-', $dateEcheance));
        //die(date_format($date,"Y-m-d"));
        $mouvement->setDateEcheance(date_create(date_format($date, "Y-m-d")));
        $em->flush($mouvement);
        $referer = $request
                ->headers
                ->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Lists all mouvement Traite entities.
     *
     * @Route("/traite", name="mouvement_traite_index", methods={"GET"})
     */
    public function traiteAction(Request $request) {
        $em = $this->getEm();
        $qb = $em->getRepository('App\\Entity\\Mouvement')->createQueryBuilder('a');
        $qb->andWhere('a.modeReglement like :modeReglement')->setParameter('modeReglement', 'Traite');
        $mvt_designation = null;
        if ($request->query->get('designation')) {
            $mvt_designation = $request->query->get('designation');
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $mvt_designation . '%');
        }
        $mvt_typeDoc = null;
        if ($request->query->get('typeDoc')) {
            $mvt_typeDoc = $request->query->get('typeDoc');
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $mvt_typeDoc);
        }
        $mvt_mouvement = null;
        $mvt_client = null;
        $mvt_fournisseur = null;
        if ($request->query->get('mouvement')) {
            $mvt_mouvement = $request->query->get('mouvement');
            if ($mvt_mouvement === 'revenu') {
                $qb->andWhere("a.mouvement = 'revenu'");
                if ($request->query->get('client')) {
                    $mvt_client = $request->query->get('client');
                    $qb->andWhere('a.client = :client')->setParameter('client', $mvt_client);
                }
            } else if ($mvt_mouvement === 'depense') {
                $qb->andWhere("a.mouvement = 'depense'");
                if ($request->query->get('fournisseur')) {
                    $mvt_fournisseur = $request->query->get('fournisseur');
                    $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $mvt_fournisseur);
                }
            }
        }
        if ($request->query->get('etat')) {
            $etats = $request->query->get('etat');
            $chaine = "(";
            for ($i = 0; $i < count($etats); $i++) {
                $chaine .= "'" . $etats[$i] . "'";
                if (count($etats) - $i > 1) {
                    $chaine .= ",";
                }
            }
            $chaine .= ")";
            $qb->andWhere('a.etat IN ' . $chaine);
        }
        $mvt_startDateCreation = null;
        $dateFormat = $this->get('app.format_date'); //service for formatting date
        if ($request->query->get('startDateCreation')) {
            $mvt_startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($mvt_startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $mvt_startDateCreation);
            }
        }
        $mvt_endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $mvt_endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($mvt_endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $mvt_endDateCreation);
            }
        }
        $mvt_startDateEcheance = null;
        if ($request->query->get('startDateEcheance')) {
            $mvt_startDateEcheance = $dateFormat->formatDate($request->query->get('startDateEcheance'));
            if ($mvt_startDateEcheance) {
                $qb->andWhere('a.dateEcheance >= :startDateEcheance')->setParameter('startDateEcheance', $mvt_startDateEcheance);
            }
        }
        $mvt_endDateEcheance = null;
        if ($request->query->get('endDateEcheance')) {
            $mvt_endDateEcheance = $dateFormat->formatDate($request->query->get('endDateEcheance'));
            if ($mvt_endDateEcheance) {
                $qb->andWhere('a.dateEcheance <= :endDateEcheance')->setParameter('endDateEcheance', $mvt_endDateEcheance);
            }
        }

        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $clients = $em->getRepository('App\\Entity\\Client')->findAll();
        $fournisseurs = $em->getRepository('App\\Entity\\Fournisseur')->findAll();

        $total = $qb->select('SUM(a.ttc) as sumDepenses')
                ->getQuery()
                ->getSingleScalarResult();
        $sumRevenus = $qb->andWhere("a.mouvement = 'revenu'")
                ->select('SUM(a.ttc) as sumRevenus')
                ->getQuery()
                ->getSingleScalarResult();


        $sumDepenses = $sumRevenus - $total;
        return $this->render('mouvement/etat/traite.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients,
                    'fournisseurs' => $fournisseurs,
                    'sumRevenus' => $sumRevenus,
                    'sumDepenses' => - $sumDepenses,
                    'total' => $sumRevenus + $sumDepenses,
                    'totalEnCoursEntrants' => $this->getTotalEtat($request, 'En cours', 'Traite','revenu'),
                    'totalEnCoursSortants' => $this->getTotalEtat($request, 'En cours', 'Traite','depense'),
                    'totalEncaissésEntrants' => $this->getTotalEtat($request, 'Encaissé', 'Traite','revenu'),
                    'totalEncaissésSortants' => $this->getTotalEtat($request, 'Encaissé', 'Traite','depense'),
                    'totalImpayésEntrants' => $this->getTotalEtat($request, 'Impayé', 'Traite','revenu'),
                    'totalImpayésSortants' => $this->getTotalEtat($request, 'Impayé', 'Traite','depense')
        ));
    }

}
