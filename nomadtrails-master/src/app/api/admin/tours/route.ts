import { auth } from "@/auth";
import pool from "@/lib/db";
import { NextResponse } from "next/server";

export async function GET() {
  const session = await auth();
  if (!session || (session.user as any).role !== 'admin') {
    return NextResponse.json({ error: "Unauthorized" }, { status: 403 });
  }
  const [rows] = await pool.query("SELECT * FROM tours ORDER BY created_at DESC");
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
      "INSERT INTO tours (slug, duration_days, price_usd, difficulty, image_url, name_en, name_ru, name_ky) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
      [data.slug, data.duration_days, data.price_usd, data.difficulty, data.image_url, data.name_en, data.name_ru, data.name_ky]
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
  await pool.query("DELETE FROM tours WHERE id = ?", [id]);
  return NextResponse.json({ success: true });
}
