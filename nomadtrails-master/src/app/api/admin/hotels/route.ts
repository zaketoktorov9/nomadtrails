import { auth } from "@/auth";
import pool from "@/lib/db";
import { NextResponse } from "next/server";

export async function GET() {
  const session = await auth();
  if (!session || (session.user as any).role !== 'admin') {
    return NextResponse.json({ error: "Unauthorized" }, { status: 403 });
  }
  const [rows] = await pool.query("SELECT * FROM hotels ORDER BY created_at DESC");
  return NextResponse.json(rows);
}

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session || (session.user as any).role !== 'admin') {
      return NextResponse.json({ error: "Unauthorized" }, { status: 403 });
    }
    const data = await req.json();
    const [result]: any = await pool.query(
      "INSERT INTO hotels (slug, type, price_per_night, image_url, name_en, name_ru, name_ky, location_en, location_ru, location_ky) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
      [data.slug, data.type, data.price_per_night, data.image_url, data.name_en, data.name_ru, data.name_ky, data.location_en, data.location_ru, data.location_ky]
    );
    return NextResponse.json({ id: result.insertId });
  } catch (err) {
    return NextResponse.json({ error: "DB Error" }, { status: 500 });
  }
}

export async function DELETE(req: Request) {
  const { searchParams } = new URL(req.url);
  const id = searchParams.get("id");
  const session = await auth();
  if (!session || (session.user as any).role !== 'admin') {
    return NextResponse.json({ error: "Unauthorized" }, { status: 403 });
  }
  await pool.query("DELETE FROM hotels WHERE id = ?", [id]);
  return NextResponse.json({ success: true });
}
