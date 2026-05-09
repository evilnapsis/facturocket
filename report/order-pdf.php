<?php
/**
 * Mecharocket - Reporte PDF
 * Reubicado en carpeta report/
 * @author @evilnapsis
 */

chdir("..");
include "core/autoload.php";
include "core/app/autoload.php";
session_start();

require('fpdf/fpdf.php');

if(!isset($_SESSION["user_id"])){
    die("Acceso denegado. Inicie sesión.");
}

if(!isset($_GET["id"])){
    die("ID no especificado.");
}

$op = OperationData::getById($_GET["id"]);
if(!$op){
    die("Operación no encontrada.");
}

$vehicle = $op->vehicle_id ? $op->getVehicle() : null;
$contact = $op->getContact();
$status = $op->getStatus();
$details = OperationDetailData::getAllByOperationId($op->id);
$jobs = JobData::getAllByOperationId($op->id);

// Config
$w_name = ConfigurationData::getByShort("workshop_name")->val;
$w_address = ConfigurationData::getByShort("workshop_address")->val;
$w_phone = ConfigurationData::getByShort("workshop_phone")->val;
$w_manager = ConfigurationData::getByShort("workshop_manager")->val;
$w_logo = ConfigurationData::getByShort("workshop_logo")->val;

class PDF extends FPDF {
    public $w_name;
    public $w_address;
    public $w_phone;
    public $w_logo;

    function Header() {
        if($this->w_logo != "" && file_exists("storage/branding/".$this->w_logo)){
            $this->Image("storage/branding/".$this->w_logo, 10, 8, 33);
            $this->SetX(45);
        }
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(30, 30, 50);
        $this->Cell(0, 10, mb_convert_encoding($this->w_name, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->SetX($this->w_logo != "" ? 45 : 10);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->MultiCell(0, 4, mb_convert_encoding($this->w_address . "\nTel: " . $this->w_phone, 'ISO-8859-1', 'UTF-8'), 0, 'L');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->w_name = $w_name;
$pdf->w_address = $w_address;
$pdf->w_phone = $w_phone;
$pdf->w_logo = $w_logo;

$pdf->AliasNbPages();
$pdf->AddPage();

// Document Title
$title = "ORDEN DE SERVICIO";
$prefix = "OS";
$contact_label = "DATOS DEL CLIENTE";

if($op->kind == 2){ $title = "PRESUPUESTO / COTIZACION"; $prefix = "PR"; }
else if($op->kind == 3){ $title = "COMPROBANTE DE VENTA"; $prefix = "VE"; }
else if($op->kind == 4){ $title = "NOTA DE ENTRADA (COMPRA)"; $prefix = "CO"; $contact_label = "DATOS DEL PROVEEDOR"; }

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(130, 10, mb_convert_encoding($title . ' #' . str_pad($op->id, 5, "0", STR_PAD_LEFT), 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 10, 'Fecha: ' . date("d/m/Y", strtotime($op->created_at)), 0, 1, 'R');
$pdf->Ln(5);

// Columns
$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($vehicle ? 95 : 190, 7, mb_convert_encoding($contact_label, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($vehicle ? 95 : 190, 6, 'Nombre: ' . mb_convert_encoding($contact ? $contact->name . " " . $contact->lastname : "Publico General", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
$pdf->Cell($vehicle ? 95 : 190, 6, mb_convert_encoding('Teléfono: ', 'ISO-8859-1', 'UTF-8') . ($contact ? $contact->phone : "-"), 0, 1, 'L');
$pdf->Cell($vehicle ? 95 : 190, 6, 'Email: ' . ($contact ? $contact->email : "-"), 0, 1, 'L');

if($vehicle){
    $pdf->SetXY($x + 100, $y);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(90, 7, 'DATOS DEL VEHICULO', 0, 1, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX($x + 100);
    $pdf->Cell(90, 6, 'Placa: ' . $vehicle->plate, 0, 1, 'L');
    $pdf->SetX($x + 100);
    $pdf->Cell(90, 6, 'Marca/Modelo: ' . mb_convert_encoding($vehicle->brand . " " . $vehicle->model, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    $pdf->SetX($x + 100);
    $pdf->Cell(90, 6, mb_convert_encoding('Color/Año: ', 'ISO-8859-1', 'UTF-8') . $vehicle->color . " / " . $vehicle->year, 0, 1, 'L');
}

$pdf->Ln(10);

// Description
if($op->description != ""){
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, mb_convert_encoding('NOTAS / DESCRIPCIÓN', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, mb_convert_encoding($op->description, 'ISO-8859-1', 'UTF-8'), 0, 'L');
    $pdf->Ln(5);
}

// Jobs Table (Only for Repairs)
if($op->kind == 1 && count($jobs)>0){
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, 'TRABAJOS Y TAREAS REALIZADAS', 0, 1, 'L', true);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(140, 7, mb_convert_encoding('Descripción', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    $pdf->Cell(50, 7, 'Estado', 1, 1, 'C');
    $pdf->SetFont('Arial', '', 9);
    foreach($jobs as $j){
        $js = $j->getStatus();
        $pdf->Cell(140, 6, mb_convert_encoding($j->name, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
        $pdf->Cell(50, 6, mb_convert_encoding($js ? $js->name : 'N/A', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
    }
    $pdf->Ln(5);
}

// Details Table
if(count($details)>0){
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, 'DETALLE DE ITEMS', 0, 1, 'L', true);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(20, 7, 'Cant.', 1, 0, 'C');
    $pdf->Cell(120, 7, 'Concepto', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Precio', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Subtotal', 1, 1, 'C');
    $pdf->SetFont('Arial', '', 9);
    foreach($details as $d){
        $name = "";
        if($d->service_id) $name = $d->getService()->name;
        else if($d->part_id) $name = $d->getPart()->name;
        
        $pdf->Cell(20, 6, $d->quantity, 1, 0, 'C');
        $pdf->Cell(120, 6, mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
        $pdf->Cell(25, 6, '$' . number_format($d->price, 2), 1, 0, 'R');
        $pdf->Cell(25, 6, '$' . number_format($d->price * $d->quantity, 2), 1, 1, 'R');
    }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(165, 8, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(25, 8, '$' . number_format($op->total, 2), 0, 1, 'R');
    $pdf->Ln(10);
}

// Signatures
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, '__________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '__________________________', 0, 1, 'C');
$pdf->Cell(95, 5, mb_convert_encoding($op->kind == 4 ? 'Firma Proveedor' : 'Firma del Cliente', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
$pdf->Cell(95, 5, mb_convert_encoding($w_manager, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->SetY($pdf->GetY()-5);
$pdf->Cell(95, 5, '', 0, 0, 'C');
$pdf->Cell(95, 5, 'Responsable Taller', 0, 1, 'C');

$pdf->Output();
?>
