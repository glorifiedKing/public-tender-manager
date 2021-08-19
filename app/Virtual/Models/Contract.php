<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Contract",
 *     description="Contract model",
 *     @OA\Xml(
 *         name="Contract"
 *     )
 * )
 */
class Contract
{

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *      title="idcontrato",
     *      description="Id of the contract",
     *      format="int64",
     *      example="3454345"
     * )
     *
     * @var string
     */
    public $idcontrato;

    /**
     * @OA\Property(
     *      title="nAnuncio",
     *      description="Anuncio",
     *      example="3798/2013"
     * )
     *
     * @var string
     */
    public $nAnuncio;

    /**
     * @OA\Property(
     *      title="tipoContrato",
     *      description="tipo Contrato",
     *      example="Empreitadas de obras públicas"
     * )
     *
     * @var string
     */
    public $tipoContrato;

    /**
     * @OA\Property(
     *      title="tipoprocedimento",
     *      description="tipo procedimento",
     *      example="Concurso público"
     * )
     *
     * @var string
     */
    public $tipoprocedimento;

    /**
     * @OA\Property(
     *      title="objectoContrato",
     *      description="objecto Contrato",
     *      example="09/2016/EMP/DGR _ EN256 Variante à ponte do Albardão, incluindo nova ponte sobre o rio Degébe"
     * )
     *
     * @var string
     */
    public $objectoContrato;

    /**
     * @OA\Property(
     *      title="adjudicantes",
     *      description="adjudicantes",
     *      example="504598686 - EP - ESTRADAS DE PORTUGAL, S.A"
     * )
     *
     * @var string
     */
    public $adjudicantes;

    /**
     * @OA\Property(
     *      title="adjudicatarios",
     *      description="adjudicantes",
     *      example="502496878 - CONSTRUÇÕES PRAGOSA, S.A."
     * )
     *
     * @var string
     */
    public $adjudicatarios;

    /**
     * @OA\Property(
     *     title="dataPublicacao",
     *     description="data Publicacao",
     *     example="2020-01-27",
     *     format="date",
     *     type="string"
     * )
     *
     * @var \Date
     */
    public $dataPublicacao;

    /**
     * @OA\Property(
     *     title="dataCelebracaoContrato",
     *     description="data Celebração Contrato",
     *     example="2020-01-27",
     *     format="date",
     *     type="string"
     * )
     *
     * @var \Date
     */
    public $dataCelebracaoContrato;


    /**
     * @OA\Property(
     *      title="precoContratual",
     *      description="preco Contratual",
     *      example="7689000.85"
     * )
     *
     * @var float
     */
    public $precoContratual;

    /**
     * @OA\Property(
     *      title="cpv",
     *      description="cpv",
     *      example="45233142-6 - Reparação de estradas"
     * )
     *
     * @var string
     */
    public $cpv;

    /**
     * @OA\Property(
     *      title="prazoExecucao",
     *      description="prazo Execucao",
     *      example="234"
     * )
     *
     * @var integer
     */
    public $prazoExecucao;

    /**
     * @OA\Property(
     *      title="localExecucao",
     *      description="local Execucao",
     *      example="Portugal, Portugal Continental"
     * )
     *
     * @var string
     */
    public $localExecucao;

    /**
     * @OA\Property(
     *      title="fundamentacao",
     *      description="fundamentacao",
     *      example="Artigo 19.º, alínea b) do Código dos Contratos Públicos"
     * )
     *
     * @var string
     */
    public $fundamentacao;

    /**
     * @OA\Property(
     *      title="Read",
     *      description="Has this contract been read",
     *      example="false"
     * )
     *
     * @var bool
     */
    public $read;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property(
     *      title="user ID",
     *      description="User's id of the new contract",
     *      format="int64",
     *      example=1
     * )
     *
     * @var integer
     */
    public $user_id;


    /**
     * @OA\Property(
     *     title="Added By",
     *     description="Contract author's user model"
     * )
     *
     * @var \App\Virtual\Models\User
     */
    private $added_by;
}
